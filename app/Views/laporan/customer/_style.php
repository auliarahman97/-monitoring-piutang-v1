<style>

.customer-report-header {
    border: 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
}

.customer-report-header .card-header {
    border: 0;
    padding: 1rem 1.25rem;
}

.report-title {
    font-weight: 700;
    margin: 0;
    font-size: 1.15rem;
}

.report-subtitle {
    margin: .2rem 0 0;
    font-size: .82rem;
    opacity: .85;
}

.filter-card {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 3px 14px rgba(0,0,0,.06);
}

.filter-card .form-control {
    border-radius: 7px;
}

.filter-label {
    font-size: .8rem;
    font-weight: 600;
    color: #495057;
}

.customer-profile {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,.06);
}

.customer-avatar {
    width: 58px;
    height: 58px;
    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1.35rem;

    background: rgba(0,123,255,.1);
    color: #007bff;
}

.customer-name {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 2px;
}

.customer-code {
    font-size: .82rem;
    color: #6c757d;
}

.customer-meta {
    font-size: .82rem;
    color: #6c757d;
}

.summary-card {
    border: 0;
    border-radius: 12px;
    min-height: 112px;

    box-shadow:
        0 3px 14px rgba(0,0,0,.06);

    transition:
        transform .15s ease,
        box-shadow .15s ease;
}

.summary-card:hover {
    transform: translateY(-2px);

    box-shadow:
        0 6px 20px rgba(0,0,0,.09);
}

.summary-label {
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .35px;
    color: #6c757d;
    font-weight: 600;
}

.summary-value {
    font-size: 1.15rem;
    font-weight: 700;
    margin-top: 3px;
}

.summary-icon {
    width: 42px;
    height: 42px;

    border-radius: 10px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 1rem;
}

.report-section {
    border: 0;
    border-radius: 12px;

    box-shadow:
        0 3px 15px rgba(0,0,0,.06);
}

.report-section .card-header {
    background: #fff;
    border-bottom: 1px solid #edf0f2;

    padding: .95rem 1.15rem;
}

.section-title {
    font-size: .98rem;
    font-weight: 700;
    margin: 0;
}

.section-subtitle {
    color: #6c757d;
    font-size: .75rem;
    margin-top: 2px;
}

.report-table {
    font-size: .82rem;
}

.report-table thead th {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;

    font-size: .74rem;
    text-transform: uppercase;
    letter-spacing: .25px;

    white-space: nowrap;
}

.report-table tbody td {
    vertical-align: middle;
}

.report-table .number {
    text-align: right;
    white-space: nowrap;
}

.report-table .date {
    text-align: center;
    white-space: nowrap;
}

.empty-state {
    padding: 55px 20px;
    text-align: center;
    color: #6c757d;
}

.empty-state i {
    font-size: 2.6rem;
    margin-bottom: 12px;
    opacity: .35;
}

.empty-state-title {
    font-weight: 600;
    color: #495057;
}

@media print {

    .main-sidebar,
    .main-header,
    .content-header,
    .filter-card,
    .btn-print {
        display: none !important;
    }

    .content-wrapper {
        margin-left: 0 !important;
    }

    .customer-report-header,
    .customer-profile,
    .summary-card,
    .report-section {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }

    .card {
        page-break-inside: avoid;
    }

}

</style>