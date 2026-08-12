<style>

@page {
    size: A4 landscape;
    margin: 12mm;
}


* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family:
        DejaVu Sans,
        Arial,
        sans-serif;

    font-size: 12px;

    color: #222;

    line-height: 1.4;

}


h1,
h2,
h3,
p {
    margin: 0;
}


table {
    border-collapse: collapse;
}


.text-center {
    text-align: center;
}


.text-right {
    text-align: right;
}


.text-left {
    text-align: left;
}


/* ================================================================
   HEADER
================================================================ */

.report-header {

    border-bottom:
        2px solid #222;

    padding-bottom:
        8px;

    margin-bottom:
        12px;

}


.report-title {

    font-size:
        18px;

    font-weight:
        bold;

    letter-spacing:
        .3px;

}


.report-subtitle {

    margin-top:
        2px;

    font-size:
        10px;

    color:
        #666;

}


/* ================================================================
   CUSTOMER
================================================================ */

.customer-table {

    width:
        100%;

    margin-bottom:
        12px;

}


.customer-table td {

    padding:
        2px 4px;

    vertical-align:
        top;

}


.customer-label {

    width:
        90px;

    font-weight:
        bold;

}


.customer-separator {

    width:
        8px;

}


/* ================================================================
   SUMMARY
================================================================ */

.summary-table {

    width:
        100%;

    margin-bottom:
        14px;

}


.summary-table td {

    width:
        25%;

    border:
        1px solid #aaa;

    padding:
        7px;

}


.summary-label {

    font-size:
        10px;

    color:
        #666;

}


.summary-value {

    margin-top:
        3px;

    font-size:
        13px;

    font-weight:
        bold;

}


/* ================================================================
   SECTION
================================================================ */

.section {

    margin-top:
        12px;

    margin-bottom:
        12px;

}


.section-title {

    font-size:
        12px;

    font-weight:
        bold;

    margin-bottom:
        5px;

    padding-bottom:
        3px;

    border-bottom:
        1px solid #999;

}


/* ================================================================
   REPORT TABLE
================================================================ */

.report-table {

    width:
        100%;

}


.report-table th {

    background:
        #eeeeee;

    border:
        1px solid #999;

    padding:
        4px;

    font-size:
        8.5px;

    font-weight:
        bold;

    text-align:
        center;

}


.report-table td {

    border:
        1px solid #aaa;

    padding:
        4px;

    font-size:
        8.5px;

    vertical-align:
        middle;

}


.report-table .number {

    text-align:
        right;

    white-space:
        nowrap;

}


.report-table .date {

    text-align:
        center;

    white-space:
        nowrap;

}


/* ================================================================
   BADGE
================================================================ */

.badge {

    display:
        inline-block;

    padding:
        2px 5px;

    border:
        1px solid #999;

    font-size:
        8px;

}


.badge-success {
    border-color: #28a745;
}


.badge-warning {
    border-color: #e0a800;
}


.badge-danger {
    border-color: #dc3545;
}


.badge-info {
    border-color: #17a2b8;
}


/* ================================================================
   EMPTY
================================================================ */

.empty {

    border:
        1px solid #aaa;

    padding:
        12px;

    text-align:
        center;

    color:
        #666;

}


/* ================================================================
   FOOTER
================================================================ */

.report-footer {

    margin-top:
        15px;

    padding-top:
        5px;

    border-top:
        1px solid #aaa;

    font-size:
        8px;

    color:
        #666;

}


/* ================================================================
   PRINT
================================================================ */

@media print {

    body {
        margin: 0;
    }

}

</style>