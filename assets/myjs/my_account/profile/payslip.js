
$(function () {

});
function payslip_table() {
    $('#payslip_table').dataTable().fnDestroy();
    payslip_tables = $('#payslip_table').DataTable
            ({
                dom: 'frtip',
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 8,
                "columnDefs": [{
                        "targets": 0,
                        "visible": false},
                    {
                        "targets": 4,
                        "visible": false},
                    {
                        "targets": 1,
                        "width": '5px'
                    }],
                oLanguage: {sProcessing: '<div><img class="zmdi-hc-spin" src="' + 'assets/images/logo.png' + '" style="width:40px; height:40px" alt="Drainwiz"><br><br><label>Processing Data...</label></div>'},

                ajax:
                        {
                            url: "Payslip/FetchPayslipTable",
                            data: {
                                profileno: $('input[name=profileno]').val(),
                                company: $('input[name=company]').val()
                            },
                            type: "POST",
                        },

                createdRow: function (row, data, dataIndex)
                {
                    console.log(data);
                    if(data[4] == 1){
                         $(row).css('background-color', '#3EB3A3');
                    }
                },
            });
    $('#payslip_table_filter').addClass('hidden');
}



$('#payslip_table').on('click', 'tr', function () {
    var data = payslip_tables.row(this).data();
    $('input[name=data]').val(data[0]);
    $("form[name=generate_payslip]").submit();

});