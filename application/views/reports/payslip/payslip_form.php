
<html>
    
    <body style="font-family: Calibri;font-size:8px;">
        <div style="position:relative">
            <div style="position:absolute;z-index: 1;margin-left: 60%;margin-top: 57%;opacity: 0.5">
               <!--<img  src="data:image;base64,<?php // echo $this->session->userdata('complogo'); ?>"  width="180" height="80">-->
            </div>
            <div style="position: absolute;top:0;left:0;right:0;left:0">
                <div style="border:solid;border-width: thin; width:100%;height:10px;text-align: center;font-weight: bold">
                    PAYSLIP
                </div>
                <div style=" width: 50%;">
                    <!--background-color: #C0C0C0-->
                    <div style=" width: 100%;display: inline-block;border:solid;border-width: thin;">
                        <table width="100%">
                            <tr style="font-size: 13px; font-weight: bold; ">
                                <td><?php echo $data['others']['empid'] ?></td>
                                <td ><?php echo $data['others']['fullname'] ?></td>
                            </tr>
                            <tr>
                                <td>Company: </td>
                                <td><?php echo $data['others']['company'] ?></td>
                            </tr>
                            <tr>
                                <td>Position: </td>
                                <td><?php echo $data['others']['jobposition'] ?></td>
                            </tr>
                        </table>
                        <h3 style="margin-left: 6px">Gross Earnings:</h3>
                        <table  frame="box" rules="cols"  width="100%" style="margin:5px; margin-top: -5px;border-width: thin;">
                            <tr >
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">Adjustments/Others
                                </td>
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">
                                </td>
                                <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT
                                </td>
                            </tr>
                            <?php echo $data['add_table']?>

                        </table>

                        <table  frame="box" rules="cols"  width="100%" style="margin:5px;border-width: thin;">
                            <tr >
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">Less
                                </td>
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">
                                </td>
                                <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT
                                </td>
                            </tr>
                             <?php echo $data['less_table']?>
                            <tr>
                                <td rowspan="1" style="border:none;">TOTAL</td>
                                <td rowspan="1" style="border:none;"></td>
                                <td rowspan="1" style="border:none;text-align:right"><?php echo $data['others']['less_total'] ?></td>
                            </tr>

                        </table>
                         <table  frame="box" rules="cols"  width="100%" style="margin:5px;border-width: thin;">
                            <tr >
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">Incentives
                                </td>
                                <td  style="background-color: #C0C0C0;border:none;" colspan="1">
                                </td>
                                <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT
                                </td>
                            </tr>
                             <?php echo $data['incentives_table']?>
                            <tr>
                                <td rowspan="1" style="border:none;">TOTAL</td>
                                <td rowspan="1" style="border:none;"></td>
                                <td rowspan="1" style="border:none;text-align:right"><?php echo $data['others']['incentive_total'] ?></td>
                            </tr>

                        </table>
                    </div>
                    <div style=" width: 100%; display: inline-block;border:solid;border-width: thin;">
                        <div>
                            <table width="100%">
                                <tr style="font-size: 13px">
                                    <td style="font-weight: bold;"></td>
                                    <td><?php echo $data['others']['schedterm'] ?></td>
                                    <td style="font-weight: bold;text-align: right" ><?php echo $data[6] ?></td>
                                </tr>
                            </table>
                            <table width="100%" style="margin-top: -2px">
                                <tr>
                                    <td style="width:40px">Rate Class: </td>
                                    <td> <?php echo $data['others']['daily_rate'] ?></td>
                                    <td style="text-align: right"><?php echo $data['others']['rate_daily'] ?></td>
                                </tr>
                                <tr>
                                    <td >Paytype: </td>
                                    <td> <?php echo $data['others']['paytype'] ?></td>
                                    <td style="text-align: right"><?php echo $data['others']['basic'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <h3 style="margin-left: 6px">Government Contributions:</h3>
                            <table  frame="box" rules="cols"  width="100%" style="margin:5px;margin-top: -5px;border-width: thin;">
                                <tr >
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1">Deductions</td>
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>
                                    <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT</td>
                                </tr> 
                                <?php echo $data['deduct_table']?>
                                <tr>
                                    <td rowspan="1" style="border:none;">TOTAL</td>
                                    <td rowspan="1" style="border:none;"></td>
                                    <td rowspan="1" style="text-align: right;border:none;"><?php echo $data['others']['deduct_total'] ?></td>
                                </tr>

                            </table>
                            
                        </div>
                        <br>
                        <div>
                            <table  frame="box" rules="cols"  width="100%" style="margin:5px;margin-top: -5px;border-width: thin;">
                                <tr >
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1">Loans</td>
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>
                                    <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT</td>
                                </tr>
                                 <?php echo $data['loans_table']?>
                                 <tr>
                                    <td rowspan="1" style="border:none;">TOTAL</td>
                                    <td rowspan="1" style="border:none;"></td>
                                    <td rowspan="1" style="text-align: right;border:none;"><?php echo $data['others']['loans_total'] ?></td>
                                </tr>
                               

                            </table>
                             <br>
                            <table  frame="box" rules="cols"  width="100%" style="margin:5px;margin-top: -5px;border-width: thin;">
                                <tr >
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1">Cash Advance</td>
                                    <td  style="background-color: #C0C0C0;border:none;" colspan="1"></td>
                                    <td  style="background-color: #C0C0C0;text-align: right;border:none;" colspan="1">AMOUNT</td>
                                </tr>
                                <?php echo $data['ca_table']?>
                                 <tr>
                                    <td rowspan="1" style="border:none;">TOTAL</td>
                                    <td rowspan="1" style="border:none;"></td>
                                    <td rowspan="1" style="text-align: right;border:none;"><?php echo $data['others']['ca_total'] ?></td>
                                </tr>
                                

                            </table>
                            
                        </div>
                        <div style="border:solid;border-width: thin;margin: 5px;margin-top: 34.0px;padding-top:-5px;padding-bottom: -5px">
                            <div style="width: 49%;display: inline-block;margin-left:10px">
                                <h2>Net Pay:</h2>
                            </div>
                            <div style="width: 47%;display: inline-block;">
                                <h2 style="text-align: right;margin-right: 10px"><?php echo $data['others']['net_pay'] ?></h2>
                            </div>
                        </div>


                    </div>

                </div>
            </div>
        </div>


    </body>
</html>