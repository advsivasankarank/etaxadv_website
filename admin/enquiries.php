<?php
require_once __DIR__ . '/_auth.php';

enq_auth_session_start();

if (isset($_GET['logout'])) {
  enq_auth_logout();
  header('Location: login.php');
  exit;
}

$currentUser = enq_auth_require_auth();
$is_admin = ($currentUser['role'] ?? '') === 'admin';

$valid_statuses = ['new', 'contacted', 'appointment_fixed', 'consultation_completed', 'converted', 'closed'];

$status_labels = [
  'new' => 'New',
  'contacted' => 'Contacted',
  'appointment_fixed' => 'Appointment Fixed',
  'consultation_completed' => 'Consultation Completed',
  'converted' => 'Converted',
  'closed' => 'Closed',
];

$status_colors = [
  'new' => ['bg' => '#fef3cd', 'text' => '#856404'],
  'contacted' => ['bg' => '#d1ecf1', 'text' => '#0c5460'],
  'appointment_fixed' => ['bg' => '#e8d5f5', 'text' => '#5b2a8c'],
  'consultation_completed' => ['bg' => '#d1f7e8', 'text' => '#0d6b4a'],
  'converted' => ['bg' => '#d4edda', 'text' => '#155724'],
  'closed' => ['bg' => '#f8d7da', 'text' => '#721c24'],
];

$contact_outcomes = [
  'Spoke to Client',
  'No Answer',
  'Call Back Requested',
  'WhatsApp Sent',
  'Email Sent',
  'Wrong Number',
  'Unreachable',
];

$confirmation_modes = ['Call', 'WhatsApp', 'Email'];
$meeting_platforms = ['Google Meet', 'Zoom', 'Microsoft Teams', 'Phone'];

$consultation_outcomes = [
  'Proposal Required',
  'Documents Awaited',
  'Follow-up Required',
  'Not Interested',
  'Converted',
];

$closure_reasons = [
  'Not Interested',
  'No Response',
  'Budget Constraint',
  'Wrong Requirement',
  'Duplicate Lead',
  'Invalid Contact',
  'Lost to Competitor',
  'Deferred for Later',
];

$enquiries_file = __DIR__ . '/../support/data/enquiries.json';

function enquiries_load_all(string $path): array
{
  if (!file_exists($path)) {
    return [];
  }

  $data = json_decode((string) file_get_contents($path), true);
  return is_array($data) ? $data : [];
}

function enquiries_save_all(string $path, array $items): void
{
  $dir = dirname($path);
  if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
  }

  file_put_contents($path, json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function enquiries_clean_text($value, int $max = 250): string
{
  return mb_substr(strip_tags(trim((string) $value)), 0, $max, 'UTF-8');
}

function enquiries_flash(string $type, string $message): void
{
  $_SESSION['enquiry_flash'] = ['type' => $type, 'message' => $message];
}

function enquiries_redirect(): void
{
  header('Location: enquiries.php');
  exit;
}

function enquiries_summary_lines(array $e): array
{
  $lines = [];

  if (!empty($e['contact_outcome'])) {
    $lines[] = 'Contacted: ' . $e['contact_outcome']
      . (!empty($e['contacted_at']) ? ' on ' . $e['contacted_at'] : '')
      . (!empty($e['handled_by']) ? ' by ' . $e['handled_by'] : '');
  }
  if (!empty($e['followup_date']) || !empty($e['followup_time'])) {
    $lines[] = 'Next follow-up: ' . trim(($e['followup_date'] ?? '') . ' ' . ($e['followup_time'] ?? ''));
  }
  if (!empty($e['appointment_mode_actual'])) {
    $appointment = 'Appointment: ' . $e['appointment_mode_actual']
      . (!empty($e['appointment_date']) ? ' on ' . $e['appointment_date'] : '')
      . (!empty($e['appointment_time']) ? ' at ' . $e['appointment_time'] : '');
    if (!empty($e['appointment_location'])) {
      $appointment .= ' at ' . $e['appointment_location'];
    }
    if (!empty($e['meeting_platform'])) {
      $appointment .= ' via ' . $e['meeting_platform'];
    }
    $lines[] = $appointment;
  }
  if (!empty($e['consultation_outcome'])) {
    $lines[] = 'Consultation outcome: ' . $e['consultation_outcome'];
  }
  if (!empty($e['service_order_no']) || !empty($e['service_finalized'])) {
    $lines[] = 'Conversion: '
      . trim(($e['service_order_no'] ?? '') . ' ' . ($e['service_finalized'] ?? ''));
  }
  if (!empty($e['closure_reason'])) {
    $lines[] = 'Closed reason: ' . $e['closure_reason'];
  }

  return $lines;
}

$enquiries = array_reverse(enquiries_load_all($enquiries_file));

$kpi = array_fill_keys($valid_statuses, 0);
foreach ($enquiries as $e) {
  $status = $e['status'] ?? 'new';
  if (isset($kpi[$status])) {
    $kpi[$status]++;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $id = (int) ($_POST['id'] ?? 0);
  $new_status = (string) ($_POST['status'] ?? '');

  if (!in_array($new_status, $valid_statuses, true)) {
    enquiries_flash('err', 'Invalid status selected.');
    enquiries_redirect();
  }

  $all = enquiries_load_all($enquiries_file);
  $found = false;

  foreach ($all as &$e) {
    if ((int) ($e['id'] ?? 0) !== $id) {
      continue;
    }

    $found = true;
    $error = null;

    $contact_outcome = enquiries_clean_text($_POST['contact_outcome'] ?? '', 100);
    $contact_notes = enquiries_clean_text($_POST['contact_notes'] ?? '', 500);
    $followup_date = enquiries_clean_text($_POST['followup_date'] ?? '', 20);
    $followup_time = enquiries_clean_text($_POST['followup_time'] ?? '', 20);

    $appointment_mode_actual = enquiries_clean_text($_POST['appointment_mode_actual'] ?? '', 20);
    $appointment_date = enquiries_clean_text($_POST['appointment_date'] ?? '', 20);
    $appointment_time = enquiries_clean_text($_POST['appointment_time'] ?? '', 20);
    $confirmation_mode = enquiries_clean_text($_POST['confirmation_mode'] ?? '', 20);
    $appointment_location = enquiries_clean_text($_POST['appointment_location'] ?? '', 150);
    $contact_person = enquiries_clean_text($_POST['contact_person'] ?? '', 100);
    $appointment_notes = enquiries_clean_text($_POST['appointment_notes'] ?? '', 500);
    $meeting_platform = enquiries_clean_text($_POST['meeting_platform'] ?? '', 50);
    $meeting_link = enquiries_clean_text($_POST['meeting_link'] ?? '', 300);
    $meeting_notes = enquiries_clean_text($_POST['meeting_notes'] ?? '', 500);

    $consultation_outcome = enquiries_clean_text($_POST['consultation_outcome'] ?? '', 100);
    $consultation_summary = enquiries_clean_text($_POST['consultation_summary'] ?? '', 1000);
    $next_step_owner = enquiries_clean_text($_POST['next_step_owner'] ?? '', 100);

    $service_order_no = enquiries_clean_text($_POST['service_order_no'] ?? '', 50);
    $service_finalized = enquiries_clean_text($_POST['service_finalized'] ?? '', 150);
    $estimated_fees = enquiries_clean_text($_POST['estimated_fees'] ?? '', 50);
    $assigned_to = enquiries_clean_text($_POST['assigned_to'] ?? '', 100);
    $expected_start_date = enquiries_clean_text($_POST['expected_start_date'] ?? '', 20);

    $closure_reason = enquiries_clean_text($_POST['closure_reason'] ?? '', 100);
    $closure_notes = enquiries_clean_text($_POST['closure_notes'] ?? '', 500);

    if ($new_status === 'contacted') {
      if ($contact_outcome === '') {
        $error = 'Please select the contact outcome.';
      }
    } elseif ($new_status === 'appointment_fixed') {
      if ($appointment_mode_actual === '' || $appointment_date === '' || $appointment_time === '') {
        $error = 'Please enter appointment mode, date and time.';
      } elseif ($appointment_mode_actual === 'Physical' && $appointment_location === '') {
        $error = 'Please enter the physical meeting location.';
      } elseif ($appointment_mode_actual === 'Online' && $meeting_platform === '') {
        $error = 'Please select the online meeting platform.';
      }
    } elseif ($new_status === 'consultation_completed') {
      if ($consultation_outcome === '' || $consultation_summary === '') {
        $error = 'Please enter the consultation outcome and summary.';
      }
    } elseif ($new_status === 'converted') {
      if ($service_order_no === '' || $service_finalized === '' || $estimated_fees === '' || $assigned_to === '' || $expected_start_date === '') {
        $error = 'Please complete all conversion details before marking as converted.';
      }
    } elseif ($new_status === 'closed') {
      if ($closure_reason === '') {
        $error = 'Please select the closure reason.';
      }
    }

    if ($error !== null) {
      enquiries_flash('err', $error);
      enquiries_redirect();
    }

    $e['status'] = $new_status;
    $e['handled_by'] = (string) ($currentUser['name'] ?? $currentUser['email'] ?? 'Team');
    $e['updated_by_email'] = (string) ($currentUser['email'] ?? '');
    $e['updated_at'] = date('c');

    if ($contact_outcome !== '') {
      $e['contact_outcome'] = $contact_outcome;
      $e['contacted_at'] = date('Y-m-d H:i');
    }
    if ($contact_notes !== '') {
      $e['contact_notes'] = $contact_notes;
    }
    $e['followup_date'] = $followup_date !== '' ? $followup_date : ($e['followup_date'] ?? null);
    $e['followup_time'] = $followup_time !== '' ? $followup_time : ($e['followup_time'] ?? null);

    if ($new_status === 'appointment_fixed') {
      $e['appointment_mode_actual'] = $appointment_mode_actual;
      $e['appointment_date'] = $appointment_date;
      $e['appointment_time'] = $appointment_time;
      $e['confirmation_mode'] = $confirmation_mode !== '' ? $confirmation_mode : null;
      $e['appointment_location'] = $appointment_mode_actual === 'Physical' ? $appointment_location : null;
      $e['contact_person'] = $appointment_mode_actual === 'Physical' ? ($contact_person !== '' ? $contact_person : null) : null;
      $e['appointment_notes'] = $appointment_notes !== '' ? $appointment_notes : null;
      $e['meeting_platform'] = $appointment_mode_actual === 'Online' ? $meeting_platform : null;
      $e['meeting_link'] = $appointment_mode_actual === 'Online' && $meeting_link !== '' ? $meeting_link : null;
      $e['meeting_notes'] = $appointment_mode_actual === 'Online' && $meeting_notes !== '' ? $meeting_notes : null;
    }

    if ($new_status === 'consultation_completed') {
      $e['consultation_outcome'] = $consultation_outcome;
      $e['appointment_outcome'] = $consultation_outcome;
      $e['consultation_summary'] = $consultation_summary;
      $e['next_step_owner'] = $next_step_owner !== '' ? $next_step_owner : null;
    }

    if ($new_status === 'converted') {
      $e['service_order_no'] = $service_order_no;
      $e['service_finalized'] = $service_finalized;
      $e['estimated_fees'] = $estimated_fees;
      $e['assigned_to'] = $assigned_to;
      $e['expected_start_date'] = $expected_start_date;
    }

    if ($new_status === 'closed') {
      $e['closure_reason'] = $closure_reason;
      $e['closure_notes'] = $closure_notes !== '' ? $closure_notes : null;
    }

    break;
  }
  unset($e);

  if (!$found) {
    enquiries_flash('err', 'Enquiry not found.');
    enquiries_redirect();
  }

  enquiries_save_all($enquiries_file, $all);
  enquiries_flash('ok', 'Workflow updated successfully.');
  enquiries_redirect();
}

$flash = $_SESSION['enquiry_flash'] ?? null;
unset($_SESSION['enquiry_flash']);

if (isset($_GET['export'])) {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="enquiries.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, [
    'ID', 'Date', 'Name', 'Mobile', 'Email', 'Organisation', 'Service', 'Mode',
    'Preferred Date', 'Preferred Time', 'Message', 'Source', 'IP', 'Status',
    'Handled By', 'Updated At', 'Contact Outcome', 'Contact Notes', 'Contacted At',
    'Follow-up Date', 'Follow-up Time', 'Appointment Mode', 'Appointment Date',
    'Appointment Time', 'Confirmation Mode', 'Appointment Location', 'Contact Person',
    'Appointment Notes', 'Meeting Platform', 'Meeting Link', 'Meeting Notes',
    'Consultation Outcome', 'Consultation Summary', 'Next Step Owner',
    'Service Order No', 'Service Finalized', 'Estimated Fees', 'Assigned To',
    'Expected Start Date', 'Closure Reason', 'Closure Notes'
  ]);
  foreach ($enquiries as $e) {
    fputcsv($out, [
      $e['id'] ?? '', $e['enquiry_date'] ?? '', $e['name'] ?? '', $e['mobile'] ?? '',
      $e['email'] ?? '', $e['organisation'] ?? '', $e['service'] ?? '', $e['consultation_mode'] ?? '',
      $e['preferred_date'] ?? '', $e['preferred_time'] ?? '', $e['message'] ?? '',
      $e['source_page'] ?? '', $e['ip_address'] ?? '', $e['status'] ?? '',
      $e['handled_by'] ?? '', $e['updated_at'] ?? '', $e['contact_outcome'] ?? '',
      $e['contact_notes'] ?? '', $e['contacted_at'] ?? '', $e['followup_date'] ?? '',
      $e['followup_time'] ?? '', $e['appointment_mode_actual'] ?? '', $e['appointment_date'] ?? '',
      $e['appointment_time'] ?? '', $e['confirmation_mode'] ?? '', $e['appointment_location'] ?? '',
      $e['contact_person'] ?? '', $e['appointment_notes'] ?? '', $e['meeting_platform'] ?? '',
      $e['meeting_link'] ?? '', $e['meeting_notes'] ?? '', $e['consultation_outcome'] ?? '',
      $e['consultation_summary'] ?? '', $e['next_step_owner'] ?? '', $e['service_order_no'] ?? '',
      $e['service_finalized'] ?? '', $e['estimated_fees'] ?? '', $e['assigned_to'] ?? '',
      $e['expected_start_date'] ?? '', $e['closure_reason'] ?? '', $e['closure_notes'] ?? ''
    ]);
  }
  fclose($out);
  exit;
}

$page_title = 'Consultation Workflow';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content">

<section class="section">
  <div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
      <div>
        <h1 style="margin:0;font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--navy);">Consultation Workflow</h1>
        <p style="margin:4px 0 0;color:var(--gray-600);font-size:14px;"><?= count($enquiries) ?> total &middot; <span style="text-transform:capitalize;"><?= $is_admin ? 'Admin' : 'BO' ?></span> &middot; <?= htmlspecialchars((string) ($currentUser['email'] ?? '')) ?></p>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <?php if ($is_admin): ?>
        <a class="btn btn-outline" href="password_management.php" style="border-color:var(--gold);color:var(--gold);">Password Management</a>
        <?php endif; ?>
        <a class="btn btn-outline" href="?export=1">Export CSV</a>
        <a class="btn btn-outline" href="?logout=1">Logout</a>
      </div>
    </div>

    <?php if (!empty($flash['message'])): ?>
      <div class="alert <?= ($flash['type'] ?? '') === 'ok' ? 'ok' : 'err' ?>" style="margin-bottom:24px;"><?= htmlspecialchars((string) $flash['message']) ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:24px;">
      <?php foreach ($valid_statuses as $s):
        $c = $status_colors[$s];
      ?>
      <div style="background:<?= $c['bg'] ?>;border-radius:12px;padding:16px 12px;text-align:center;">
        <div style="font-size:28px;font-weight:700;color:<?= $c['text'] ?>;line-height:1.1;"><?= $kpi[$s] ?></div>
        <div style="font-size:11px;font-weight:600;color:<?= $c['text'] ?>;margin-top:4px;text-transform:uppercase;letter-spacing:.5px;"><?= $status_labels[$s] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="card" style="padding:18px 20px;margin-bottom:24px;background:#f8fafc;border:1px solid var(--gray-100);">
      <div style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;font-size:13px;color:var(--gray-600);">
        <div><strong style="display:block;color:var(--navy);margin-bottom:4px;">Contacted</strong>Record the communication outcome and next follow-up.</div>
        <div><strong style="display:block;color:var(--navy);margin-bottom:4px;">Appointment Fixed</strong>Capture physical or online scheduling details.</div>
        <div><strong style="display:block;color:var(--navy);margin-bottom:4px;">Consultation Completed</strong>Store advisory summary and next action owner.</div>
        <div><strong style="display:block;color:var(--navy);margin-bottom:4px;">Converted</strong>Record commercial and delivery handover details.</div>
        <div><strong style="display:block;color:var(--navy);margin-bottom:4px;">Closed</strong>Mark the closure reason for reporting and review.</div>
      </div>
    </div>

    <?php if (empty($enquiries)): ?>
      <div style="text-align:center;padding:80px 20px;color:var(--gray-400);font-size:16px;">No enquiries yet.</div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
          <thead>
            <tr style="background:var(--gray-50);">
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">#</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Date</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Client</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Contact</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Service</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Mode</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Status</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);min-width:360px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enquiries as $e):
              $cur_status = $e['status'] ?? 'new';
              $sc = $status_colors[$cur_status] ?? $status_colors['new'];
              $summary_lines = enquiries_summary_lines($e);
            ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td style="padding:10px 12px;vertical-align:top;color:var(--gray-400);"><?= $e['id'] ?? '-' ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= date('d-m-Y', strtotime($e['enquiry_date'] ?? 'now')) ?><br><span style="font-size:11px;color:var(--gray-400);"><?= date('h:i A', strtotime($e['enquiry_date'] ?? 'now')) ?></span></td>
              <td style="padding:10px 12px;vertical-align:top;font-weight:600;"><?= htmlspecialchars((string) ($e['name'] ?? '')) ?><?= !empty($e['organisation']) ? '<br><span style="font-size:11px;color:var(--gray-400);">' . htmlspecialchars((string) $e['organisation']) . '</span>' : '' ?></td>
              <td style="padding:10px 12px;vertical-align:top;">
                <a href="tel:<?= htmlspecialchars((string) ($e['mobile'] ?? '')) ?>" style="color:var(--navy);white-space:nowrap;"><?= htmlspecialchars((string) ($e['mobile'] ?? '')) ?></a><br>
                <a href="mailto:<?= htmlspecialchars((string) ($e['email'] ?? '')) ?>" style="color:var(--gray-400);font-size:11px;word-break:break-all;"><?= htmlspecialchars((string) ($e['email'] ?? '')) ?></a>
              </td>
              <td style="padding:10px 12px;vertical-align:top;"><?= htmlspecialchars((string) ($e['service'] ?? '')) ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= htmlspecialchars((string) ($e['consultation_mode'] ?? '-')) ?><?= !empty($e['preferred_date']) ? '<br><span style="font-size:11px;color:var(--gray-400);">' . htmlspecialchars((string) $e['preferred_date']) . (!empty($e['preferred_time']) ? ' ' . htmlspecialchars((string) $e['preferred_time']) : '') . '</span>' : '' ?></td>
              <td style="padding:10px 12px;vertical-align:top;">
                <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>;"><?= htmlspecialchars((string) ($status_labels[$cur_status] ?? $cur_status)) ?></span>
              </td>
              <td style="padding:10px 12px;vertical-align:top;">
                <form method="post" class="workflow-form" style="display:flex;flex-direction:column;gap:8px;background:#fff;border:1px solid var(--gray-100);border-radius:12px;padding:12px;">
                  <input type="hidden" name="id" value="<?= (int) ($e['id'] ?? 0) ?>">
                  <select name="status" class="status-select input" style="font-size:12px;min-width:140px;">
                    <?php foreach ($valid_statuses as $s): ?>
                    <option value="<?= htmlspecialchars($s) ?>" <?= $cur_status === $s ? 'selected' : '' ?>><?= htmlspecialchars($status_labels[$s]) ?></option>
                    <?php endforeach; ?>
                  </select>

                  <div class="stage-panel" data-status="contacted" style="display:<?= $cur_status === 'contacted' ? 'grid' : 'none' ?>;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                    <div class="field" style="margin:0;">
                      <label>Contact Outcome</label>
                      <select name="contact_outcome" class="input">
                        <option value="">Select outcome</option>
                        <?php foreach ($contact_outcomes as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= ($e['contact_outcome'] ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Next Follow-up Date</label>
                      <input class="input" type="date" name="followup_date" value="<?= htmlspecialchars((string) ($e['followup_date'] ?? '')) ?>">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Next Follow-up Time</label>
                      <input class="input" type="time" name="followup_time" value="<?= htmlspecialchars((string) ($e['followup_time'] ?? '')) ?>">
                    </div>
                    <div class="field" style="margin:0;grid-column:1 / -1;">
                      <label>Remarks</label>
                      <textarea class="input" name="contact_notes" rows="2" placeholder="What happened in the call or follow-up?"><?= htmlspecialchars((string) ($e['contact_notes'] ?? '')) ?></textarea>
                    </div>
                  </div>

                  <div class="stage-panel" data-status="appointment_fixed" style="display:<?= $cur_status === 'appointment_fixed' ? 'grid' : 'none' ?>;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                    <div class="field" style="margin:0;">
                      <label>Appointment Mode</label>
                      <select name="appointment_mode_actual" class="input appointment-mode-select">
                        <option value="">Select mode</option>
                        <option value="Physical" <?= ($e['appointment_mode_actual'] ?? '') === 'Physical' ? 'selected' : '' ?>>Physical</option>
                        <option value="Online" <?= ($e['appointment_mode_actual'] ?? '') === 'Online' ? 'selected' : '' ?>>Online</option>
                      </select>
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Confirmed By</label>
                      <select name="confirmation_mode" class="input">
                        <option value="">Select method</option>
                        <?php foreach ($confirmation_modes as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= ($e['confirmation_mode'] ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Appointment Date</label>
                      <input class="input" type="date" name="appointment_date" value="<?= htmlspecialchars((string) ($e['appointment_date'] ?? '')) ?>">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Appointment Time</label>
                      <input class="input" type="time" name="appointment_time" value="<?= htmlspecialchars((string) ($e['appointment_time'] ?? '')) ?>">
                    </div>
                    <div class="appointment-physical" style="display:<?= ($e['appointment_mode_actual'] ?? '') === 'Physical' ? 'contents' : 'none' ?>;">
                      <div class="field" style="margin:0;">
                        <label>Office / Meeting Location</label>
                        <input class="input" type="text" name="appointment_location" value="<?= htmlspecialchars((string) ($e['appointment_location'] ?? '')) ?>" placeholder="Meeting venue or branch office">
                      </div>
                      <div class="field" style="margin:0;">
                        <label>Contact Person</label>
                        <input class="input" type="text" name="contact_person" value="<?= htmlspecialchars((string) ($e['contact_person'] ?? '')) ?>" placeholder="Who will receive the client?">
                      </div>
                    </div>
                    <div class="appointment-online" style="display:<?= ($e['appointment_mode_actual'] ?? '') === 'Online' ? 'contents' : 'none' ?>;">
                      <div class="field" style="margin:0;">
                        <label>Meeting Platform</label>
                        <select name="meeting_platform" class="input">
                          <option value="">Select platform</option>
                          <?php foreach ($meeting_platforms as $option): ?>
                          <option value="<?= htmlspecialchars($option) ?>" <?= ($e['meeting_platform'] ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="field" style="margin:0;">
                        <label>Meeting Link</label>
                        <input class="input" type="url" name="meeting_link" value="<?= htmlspecialchars((string) ($e['meeting_link'] ?? '')) ?>" placeholder="Paste Google Meet / Zoom / Teams link">
                      </div>
                      <div style="grid-column:1 / -1;font-size:11px;color:var(--gray-500);background:#f8fafc;border:1px dashed var(--gray-200);border-radius:10px;padding:10px 12px;">
                        For online meetings, create the link in your own account and paste it here.
                        <a href="https://meet.google.com/new" target="_blank" rel="noopener noreferrer">Create Google Meet</a>
                        |
                        <a href="https://zoom.us/meeting/schedule" target="_blank" rel="noopener noreferrer">Schedule Zoom</a>
                      </div>
                      <div class="field" style="margin:0;grid-column:1 / -1;">
                        <label>Online Meeting Notes</label>
                        <textarea class="input" name="meeting_notes" rows="2" placeholder="Any joining instructions or internal notes"><?= htmlspecialchars((string) ($e['meeting_notes'] ?? '')) ?></textarea>
                      </div>
                    </div>
                    <div class="field" style="margin:0;grid-column:1 / -1;">
                      <label>Staff Remarks</label>
                      <textarea class="input" name="appointment_notes" rows="2" placeholder="Scheduling notes, confirmations, or special instructions"><?= htmlspecialchars((string) ($e['appointment_notes'] ?? '')) ?></textarea>
                    </div>
                  </div>

                  <div class="stage-panel" data-status="consultation_completed" style="display:<?= $cur_status === 'consultation_completed' ? 'grid' : 'none' ?>;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                    <div class="field" style="margin:0;">
                      <label>Consultation Outcome</label>
                      <select name="consultation_outcome" class="input">
                        <option value="">Select outcome</option>
                        <?php foreach ($consultation_outcomes as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= (($e['consultation_outcome'] ?? ($e['appointment_outcome'] ?? '')) === $option) ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Next Step Owner</label>
                      <input class="input" type="text" name="next_step_owner" value="<?= htmlspecialchars((string) ($e['next_step_owner'] ?? '')) ?>" placeholder="Who owns the next action?">
                    </div>
                    <div class="field" style="margin:0;grid-column:1 / -1;">
                      <label>Consultation Summary</label>
                      <textarea class="input" name="consultation_summary" rows="3" placeholder="Key points discussed, decision drivers and next steps"><?= htmlspecialchars((string) ($e['consultation_summary'] ?? '')) ?></textarea>
                    </div>
                  </div>

                  <div class="stage-panel" data-status="converted" style="display:<?= $cur_status === 'converted' ? 'grid' : 'none' ?>;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                    <div class="field" style="margin:0;">
                      <label>Service Order / Engagement Ref</label>
                      <input class="input" type="text" name="service_order_no" value="<?= htmlspecialchars((string) ($e['service_order_no'] ?? '')) ?>">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Service Finalized</label>
                      <input class="input" type="text" name="service_finalized" value="<?= htmlspecialchars((string) ($e['service_finalized'] ?? '')) ?>" placeholder="GST litigation, payroll, company compliance">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Commercial Value / Fee</label>
                      <input class="input" type="text" name="estimated_fees" value="<?= htmlspecialchars((string) ($e['estimated_fees'] ?? '')) ?>" placeholder="Quoted or approved fee">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Assigned Team Member</label>
                      <input class="input" type="text" name="assigned_to" value="<?= htmlspecialchars((string) ($e['assigned_to'] ?? '')) ?>">
                    </div>
                    <div class="field" style="margin:0;">
                      <label>Expected Kickoff Date</label>
                      <input class="input" type="date" name="expected_start_date" value="<?= htmlspecialchars((string) ($e['expected_start_date'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="stage-panel" data-status="closed" style="display:<?= $cur_status === 'closed' ? 'grid' : 'none' ?>;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;">
                    <div class="field" style="margin:0;">
                      <label>Closure Reason</label>
                      <select name="closure_reason" class="input">
                        <option value="">Select reason</option>
                        <?php foreach ($closure_reasons as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" <?= ($e['closure_reason'] ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="field" style="margin:0;grid-column:1 / -1;">
                      <label>Closure Note</label>
                      <textarea class="input" name="closure_notes" rows="2" placeholder="Why was the lead closed?"><?= htmlspecialchars((string) ($e['closure_notes'] ?? '')) ?></textarea>
                    </div>
                  </div>

                  <button class="btn btn-sm btn-primary" type="submit" name="update_status" style="min-height:auto;padding:8px 12px;font-size:12px;">Update Workflow</button>
                </form>
              </td>
            </tr>
            <?php if (!empty($summary_lines)): ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td></td>
              <td colspan="7" style="padding:0 12px 10px;">
                <div style="background:#f8fafc;border:1px solid var(--gray-100);border-radius:10px;padding:10px 12px;font-size:12px;color:var(--gray-600);line-height:1.6;">
                  <strong style="color:var(--navy);">Workflow Notes:</strong>
                  <?= htmlspecialchars(implode(' | ', $summary_lines)) ?>
                </div>
              </td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($e['message'])): ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td colspan="8" style="padding:0 12px 10px;color:var(--gray-600);font-size:12px;line-height:1.5;white-space:pre-wrap;"><?= htmlspecialchars((string) $e['message']) ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

</main>

<script>
document.querySelectorAll('.workflow-form').forEach(function(form) {
  var statusSelect = form.querySelector('.status-select');
  var modeSelect = form.querySelector('.appointment-mode-select');

  function togglePanels() {
    var selectedStatus = statusSelect.value;
    form.querySelectorAll('.stage-panel').forEach(function(panel) {
      panel.style.display = panel.getAttribute('data-status') === selectedStatus ? 'grid' : 'none';
    });
  }

  function toggleAppointmentMode() {
    var mode = modeSelect ? modeSelect.value : '';
    var physical = form.querySelector('.appointment-physical');
    var online = form.querySelector('.appointment-online');

    if (physical) {
      physical.style.display = mode === 'Physical' ? 'contents' : 'none';
    }
    if (online) {
      online.style.display = mode === 'Online' ? 'contents' : 'none';
    }
  }

  statusSelect.addEventListener('change', togglePanels);
  if (modeSelect) {
    modeSelect.addEventListener('change', toggleAppointmentMode);
  }

  togglePanels();
  toggleAppointmentMode();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
