<div class="box-body" style="max-width: 100%;  overflow: auto;">
    <table name="change_schedule_table" class="table table-responsive  table-hover" >
        <thead id="cs_thead" style="background-color:#2692D0;color:white;">
            <tr>
                <th >Action</th>
                <th ></th>
                <th style="white-space:nowrap;">Department</th>
                <th style="white-space:nowrap;">Date Filed</th>
                <th style="white-space:nowrap;">Employee<br><input type="text" class="form-control" onkeyup="" name="cs_employeename"></th>
                <th style="white-space:nowrap;">Category<br><select class="form-control" onchange="" name="cs_type_search">
                        <option value="">All</option>
                        <option value="0">Shift Change</option>
                        <option value="1">Straight Duty</option>
                        <option value="2">Cancel Day Off</option>
                        <option value="3">Change Day Off</option>
                    </select></th>
                <th style="white-space:nowrap;">From (shift date & time)</th>
                <th style="white-space:nowrap;">To (shift date & time)</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

