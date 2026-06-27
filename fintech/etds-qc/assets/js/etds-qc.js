document.addEventListener('click', function (event) {
  const target = event.target;
  if (!(target instanceof HTMLElement)) {
    return;
  }
  const sidebarToggle = target.closest('[data-sidebar-toggle]');
  if (sidebarToggle) {
    document.body.classList.add('is-sidebar-open');
    return;
  }
  const sidebarClose = target.closest('[data-sidebar-close]');
  if (sidebarClose) {
    document.body.classList.remove('is-sidebar-open');
    return;
  }
  if (target.matches('[data-confirm]')) {
    const message = target.getAttribute('data-confirm') || 'Are you sure?';
    if (!window.confirm(message)) {
      event.preventDefault();
    }
  }
});

document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    document.body.classList.remove('is-sidebar-open');
  }
});

document.addEventListener('DOMContentLoaded', function () {
  document.body.classList.remove('is-sidebar-open');

  document.querySelectorAll('[data-dropzone]').forEach(function (dropzone) {
    if (!(dropzone instanceof HTMLElement)) {
      return;
    }
    const input = dropzone.querySelector('input[type="file"]');
    if (!(input instanceof HTMLInputElement)) {
      return;
    }
    ['dragenter', 'dragover'].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (event) {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
      });
    });
    ['dragleave', 'drop'].forEach(function (eventName) {
      dropzone.addEventListener(eventName, function (event) {
        event.preventDefault();
        dropzone.classList.remove('is-dragover');
      });
    });
    dropzone.addEventListener('drop', function (event) {
      const dragEvent = event;
      if (!dragEvent.dataTransfer || !dragEvent.dataTransfer.files) {
        return;
      }
      input.files = dragEvent.dataTransfer.files;
    });
  });
});

document.addEventListener('submit', async function (event) {
  const form = event.target;
  if (!(form instanceof HTMLFormElement)) {
    return;
  }
  if (form.getAttribute('data-ajax') !== 'reload') {
    return;
  }

  event.preventDefault();
  const submitButton = form.querySelector('button[type="submit"]');
  const progress = form.querySelector('[data-upload-progress]');
  const progressBar = progress ? progress.querySelector('span') : null;
  const previousText = submitButton ? submitButton.textContent : '';
  if (submitButton) {
    submitButton.disabled = true;
    submitButton.textContent = 'Working...';
  }
  if (progress instanceof HTMLElement) {
    progress.hidden = false;
  }
  if (progressBar instanceof HTMLElement) {
    progressBar.style.width = '0%';
  }

  try {
    const formData = new FormData(form);
    formData.set('ajax', '1');
    const payload = await new Promise(function (resolve, reject) {
      const xhr = new XMLHttpRequest();
      xhr.open(form.method || 'POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      xhr.responseType = 'json';
      xhr.upload.addEventListener('progress', function (progressEvent) {
        if (!progressEvent.lengthComputable || !(progressBar instanceof HTMLElement)) {
          return;
        }
        const percent = Math.max(5, Math.round((progressEvent.loaded / progressEvent.total) * 100));
        progressBar.style.width = percent + '%';
      });
      xhr.onload = function () {
        const responsePayload = xhr.response;
        const contentType = xhr.getResponseHeader('content-type') || '';
        if (!contentType.includes('application/json') || !responsePayload) {
          reject(new Error('Unexpected response'));
          return;
        }
        if (xhr.status < 200 || xhr.status >= 300 || !responsePayload.ok) {
          reject(new Error(responsePayload.message || 'Request failed.'));
          return;
        }
        resolve(responsePayload);
      };
      xhr.onerror = function () {
        reject(new Error('Request failed.'));
      };
      xhr.send(formData);
    });
    if (progressBar instanceof HTMLElement) {
      progressBar.style.width = '100%';
    }
    window.location.href = payload.redirect || window.location.href;
  } catch (error) {
    HTMLFormElement.prototype.submit.call(form);
    return;
  } finally {
    if (submitButton) {
      submitButton.disabled = false;
      submitButton.textContent = previousText;
    }
    if (progress instanceof HTMLElement) {
      window.setTimeout(function () {
        progress.hidden = true;
      }, 400);
    }
  }
});

document.addEventListener('DOMContentLoaded', function () {
  const grid = document.querySelector('[data-spreadsheet-grid]');
  if (!(grid instanceof HTMLElement)) {
    return;
  }

  const sessionId = grid.getAttribute('data-session-id') || '';
  const sheet = grid.getAttribute('data-sheet') || 'deductees';
  const csrf = grid.getAttribute('data-csrf') || '';
  const undoStack = [];
  const redoStack = [];
  let sortDirection = 1;
  let lastSortField = '';

  function selectedRows() {
    return Array.from(grid.querySelectorAll('[data-row-select]:checked')).map(function (input) {
      return input instanceof HTMLInputElement ? input.value : '';
    }).filter(Boolean);
  }

  async function postWorkspace(payload) {
    const body = new FormData();
    Object.entries(payload).forEach(function ([key, value]) {
      if (Array.isArray(value)) {
        value.forEach(function (item) {
          body.append(key + '[]', String(item));
        });
      } else {
        body.append(key, String(value));
      }
    });
    body.set('session_id', sessionId);
    body.set('sheet', sheet);
    body.set('_csrf', csrf);
    body.set('ajax', '1');

    const response = await fetch(window.location.pathname + window.location.search, {
      method: 'POST',
      body: body,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const data = await response.json();
    if (!response.ok || !data.ok) {
      throw new Error(data.message || 'Workspace request failed.');
    }
    return data;
  }

  function statusLabel(status) {
    return String(status || 'valid').replace(/_/g, ' ').toUpperCase();
  }

  function setCellStatus(cell, status) {
    if (!(cell instanceof HTMLElement)) {
      return;
    }
    cell.dataset.status = status;
    cell.classList.remove('is-valid', 'is-warning', 'is-error', 'is-corrected', 'is-ai_suggested', 'is-manual_override', 'is-ignored');
    cell.classList.add('is-' + status);
    const label = cell.querySelector('.cell__status');
    if (label instanceof HTMLElement) {
      label.textContent = statusLabel(status);
    }
  }

  async function saveCell(editor, mode, reason) {
    if (!(editor instanceof HTMLElement)) {
      return;
    }
    const recordId = editor.getAttribute('data-record-id') || '';
    const field = editor.getAttribute('data-field') || '';
    const previousValue = editor.getAttribute('data-original-value') || '';
    const nextValue = editor.textContent !== null ? editor.textContent.trim() : '';
    if (!recordId || !field || nextValue === previousValue) {
      return;
    }

    const cell = editor.closest('.cell');
    try {
      const result = await postWorkspace({
        action: 'workspace_edit',
        record_id: recordId,
        field: field,
        value: nextValue,
        reason: reason || '',
        mode: mode || 'manual_override'
      });
      editor.setAttribute('data-original-value', String(result.value ?? nextValue));
      undoStack.push({ editor: editor, before: previousValue, after: nextValue, field: field, recordId: recordId });
      redoStack.length = 0;
      setCellStatus(cell, mode === 'ai_suggested' ? 'corrected' : 'manual_override');
    } catch (error) {
      editor.textContent = previousValue;
      window.alert(error instanceof Error ? error.message : 'Cell update failed.');
    }
  }

  grid.addEventListener('focusout', function (event) {
    const target = event.target;
    if (!(target instanceof HTMLElement) || !target.matches('[data-cell-editor]')) {
      return;
    }
    saveCell(target, 'manual_override', '');
  });

  grid.addEventListener('keydown', function (event) {
    const target = event.target;
    if (!(target instanceof HTMLElement) || !target.matches('[data-cell-editor]')) {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'z' && undoStack.length > 0) {
        event.preventDefault();
        const entry = undoStack.pop();
        if (entry) {
          entry.editor.textContent = entry.before;
          saveCell(entry.editor, 'manual_override', 'Undo change');
          redoStack.push(entry);
        }
      }
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'y' && redoStack.length > 0) {
        event.preventDefault();
        const entry = redoStack.pop();
        if (entry) {
          entry.editor.textContent = entry.after;
          saveCell(entry.editor, 'manual_override', 'Redo change');
          undoStack.push(entry);
        }
      }
      return;
    }

    const cells = Array.from(grid.querySelectorAll('[data-cell-editor]'));
    const index = cells.indexOf(target);
    const moveFocus = function (offset) {
      const next = cells[index + offset];
      if (next instanceof HTMLElement) {
        next.focus();
        document.getSelection()?.selectAllChildren(next);
      }
    };

    if (event.key === 'Enter') {
      event.preventDefault();
      saveCell(target, 'manual_override', '');
      moveFocus(1);
    } else if (event.key === 'ArrowRight' && target.textContent === document.getSelection()?.toString()) {
      moveFocus(1);
    } else if (event.key === 'ArrowLeft' && target.textContent === document.getSelection()?.toString()) {
      moveFocus(-1);
    } else if (event.key === 'ArrowDown') {
      const columns = target.closest('tr')?.querySelectorAll('[data-cell-editor]').length || 1;
      moveFocus(columns);
    } else if (event.key === 'ArrowUp') {
      const columns = target.closest('tr')?.querySelectorAll('[data-cell-editor]').length || 1;
      moveFocus(-columns);
    }
  });

  grid.addEventListener('paste', function (event) {
    const target = event.target;
    if (!(target instanceof HTMLElement) || !target.matches('[data-cell-editor]')) {
      return;
    }
    const pasted = event.clipboardData?.getData('text/plain') || '';
    if (!pasted.includes('\t') && !pasted.includes('\n')) {
      return;
    }
    event.preventDefault();
    const rows = pasted.split(/\r?\n/).filter(Boolean).map(function (line) { return line.split('\t'); });
    const editors = Array.from(grid.querySelectorAll('[data-cell-editor]'));
    const startIndex = editors.indexOf(target);
    const columnCount = target.closest('tr')?.querySelectorAll('[data-cell-editor]').length || 1;
    rows.forEach(function (rowValues, rowOffset) {
      rowValues.forEach(function (value, columnOffset) {
        const editor = editors[startIndex + (rowOffset * columnCount) + columnOffset];
        if (editor instanceof HTMLElement) {
          editor.textContent = value;
          saveCell(editor, 'manual_override', 'Multi-cell paste');
        }
      });
    });
  });

  grid.addEventListener('click', async function (event) {
    const target = event.target;
    if (!(target instanceof HTMLElement)) {
      return;
    }

    const applySuggestion = target.closest('[data-apply-suggestion]');
    if (applySuggestion instanceof HTMLElement) {
      const recordId = applySuggestion.getAttribute('data-record-id') || '';
      const field = applySuggestion.getAttribute('data-field') || '';
      const value = applySuggestion.getAttribute('data-value') || '';
      const reason = applySuggestion.getAttribute('data-reason') || 'Applied AI suggestion';
      const editor = grid.querySelector('[data-cell-editor][data-record-id="' + CSS.escape(recordId) + '"][data-field="' + CSS.escape(field) + '"]');
      const cell = editor?.closest('.cell') || null;
      try {
        await postWorkspace({
          action: 'workspace_apply_suggestion',
          record_id: recordId,
          field: field,
          suggested_value: value,
          reason: reason
        });
        if (editor instanceof HTMLElement) {
          editor.textContent = value;
          editor.setAttribute('data-original-value', value);
        }
        setCellStatus(cell, 'corrected');
        applySuggestion.closest('.cell__suggestion')?.remove();
      } catch (error) {
        window.alert(error instanceof Error ? error.message : 'Suggestion could not be applied.');
      }
      return;
    }

    const ignoreSuggestion = target.closest('[data-ignore-suggestion]');
    if (ignoreSuggestion instanceof HTMLElement) {
      const recordId = ignoreSuggestion.getAttribute('data-record-id') || '';
      const field = ignoreSuggestion.getAttribute('data-field') || '';
      const editor = grid.querySelector('[data-cell-editor][data-record-id="' + CSS.escape(recordId) + '"][data-field="' + CSS.escape(field) + '"]');
      const cell = editor?.closest('.cell') || null;
      try {
        await postWorkspace({
          action: 'workspace_ignore_suggestion',
          record_id: recordId,
          field: field
        });
        setCellStatus(cell, 'ignored');
        ignoreSuggestion.closest('.cell__suggestion')?.remove();
      } catch (error) {
        window.alert(error instanceof Error ? error.message : 'Suggestion could not be ignored.');
      }
      return;
    }

    const sortButton = target.closest('th[data-sort-field]');
    if (sortButton instanceof HTMLElement) {
      const field = sortButton.getAttribute('data-sort-field') || '';
      if (!field) {
        return;
      }
      if (lastSortField === field) {
        sortDirection *= -1;
      } else {
        lastSortField = field;
        sortDirection = 1;
      }
      const tbody = grid.querySelector('tbody');
      if (!(tbody instanceof HTMLElement)) {
        return;
      }
      const rows = Array.from(tbody.querySelectorAll('tr'));
      rows.sort(function (left, right) {
        const leftValue = left.querySelector('[data-field="' + CSS.escape(field) + '"] [data-cell-editor]')?.textContent?.trim() || '';
        const rightValue = right.querySelector('[data-field="' + CSS.escape(field) + '"] [data-cell-editor]')?.textContent?.trim() || '';
        return leftValue.localeCompare(rightValue, undefined, { numeric: true, sensitivity: 'base' }) * sortDirection;
      });
      rows.forEach(function (row) { tbody.appendChild(row); });
    }
  });

  const selectAll = grid.querySelector('[data-select-all]');
  if (selectAll instanceof HTMLInputElement) {
    selectAll.addEventListener('change', function () {
      grid.querySelectorAll('[data-row-select]').forEach(function (checkbox) {
        if (checkbox instanceof HTMLInputElement) {
          checkbox.checked = selectAll.checked;
          checkbox.closest('tr')?.classList.toggle('is-selected', checkbox.checked);
        }
      });
    });
  }

  grid.addEventListener('change', function (event) {
    const target = event.target;
    if (target instanceof HTMLInputElement && target.matches('[data-row-select]')) {
      target.closest('tr')?.classList.toggle('is-selected', target.checked);
    }
  });

  const searchInput = grid.querySelector('[data-sheet-search]');
  if (searchInput instanceof HTMLInputElement) {
    searchInput.addEventListener('input', function () {
      const needle = searchInput.value.trim().toLowerCase();
      grid.querySelectorAll('tbody tr').forEach(function (row) {
        if (!(row instanceof HTMLElement)) {
          return;
        }
        const text = row.textContent?.toLowerCase() || '';
        row.hidden = needle !== '' && !text.includes(needle);
      });
    });
  }
});
