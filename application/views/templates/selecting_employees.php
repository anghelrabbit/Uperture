<table name="employees_table" class="table table-striped table-bordered" style="width:100%;">
    <thead  style="background-color:#357CA5;color:white;">
        <tr>
            <th></th>
            <th></th>
            <th>
                <input type="checkbox" name="check_all" onclick="checkReferences()" style="width:20px;height:20px;">
            </th>
            <th>Employee<br>
                <input name="employees_lastname" class="form-control" type="text" placeholder="Lastname" style="color:black"/>
                <input name="employees_firstname" class="form-control" type="text"  placeholder="Firstname" style="color:black"/>
            </th>
            <th > Years of Service
                <br>
                <input  type="number" class="form-control" placeholder="Years" style="color:black;" name="employees_years" />
                <input  type="number" class="form-control" placeholder="Months" style="color:black;" name="employees_months"/>
            </th>
            <th>Job Position</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>