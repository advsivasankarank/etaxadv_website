# Reconciliation Rule Matrix

| Rule Code | Rule | Severity | Description | Effect |
| --- | --- | --- | --- | --- |
| `REC_TOTAL_MATCH` | Deductee total must not exceed challan available total | critical | Total allocated tax cannot be more than available challan funds | block ready state |
| `REC_SHORT_UTIL` | Challan allocation is lower than deductee mapped total | critical | Deductees have more assigned tax than challan supports | block ready state |
| `REC_EXCESS_BAL` | Challan has unused remaining balance | warning | Unallocated balance exists after mapping | review required |
| `REC_NO_CHALLAN` | Deductee record has no challan allocation | critical | Deductee tax exists without challan backing | block ready state |
| `REC_MISSING_CHALLAN` | Challan reference points to non-existent challan | critical | Data references an unavailable challan | block ready state |
| `REC_MULTI_ALLOC` | Deductee allocation split produces imbalance | warning | Multi-challan allocations require review | review required |
| `REC_SECTION_MISMATCH` | Section code is inconsistent between record set and challan | warning | Indicates possible wrong challan mapping | review required |
| `REC_DEPOSIT_DATE` | Challan deposit date falls outside expected period | warning | Deposit timing needs confirmation | review required |

## Reconciliation Dashboard Metrics

- challan total
- allocated total
- deductee total
- balance
- difference
- matched item count
- unmatched item count
- open review count

## Scoring Guidance

- start at `100`
- subtract `15` per unresolved critical exception
- subtract `5` per unresolved warning
- `difference = challan_total - allocated_total`
- ready state requires `difference = 0` and no unresolved critical items

## Recommended Console Sections

- matched challans
- unmatched challans
- deductees without allocation
- over-allocated challans
- unused challan balances
