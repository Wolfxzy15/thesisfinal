<div id="unreg">
        <p>Evacuation Center 1</p>
    </div>

    <table class="table table-hover table-bordered table-light">
        <thead>
            <tr>
                <th scope="col">Family ID#</th> 
                <th scope="col">Number of Members</th>
                <th scope="col">Number of PWD</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
                
                
                $sql = "SELECT * FROM tbl_families WHERE evacID = 2";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)){
                        $family_id = $row['family_id'];
                        $num_members = $row['num_members'];
                        $num_pwd = $row['num_pwd'];
                        $latitude = $row['latitude'];
                        $longitude = $row['longitude'];

                        echo '<tr>
                                <input type="hidden" name="latitude" value="' . $latitude .'">
                                <input type="hidden" name="longitude" value="' . $longitude . '">
                                <input type="hidden" name="familyid" value="' . $family_id . '">
                                <th scope="row">'. $family_id .'</th>
                                <td>'.$num_members.'</td>
                                <td>'.$num_pwd.'</td>
                                ';
                    }
                }
                
            ?>
        </tbody>
    </table>
    <div id="unreg">
        <p>Evacuation Center 2</p>
    </div>

    <table class="table table-hover table-bordered table-light">
        <thead>
            <tr>
                <th scope="col">Family ID#</th> 
                <th scope="col">Number of Members</th>
                <th scope="col">Number of PWD</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
                
                
                $sql = "SELECT * FROM tbl_families WHERE evacID = 3";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)){
                        $family_id = $row['family_id'];
                        $num_members = $row['num_members'];
                        $num_pwd = $row['num_pwd'];
                        $latitude = $row['latitude'];
                        $longitude = $row['longitude'];

                        echo '<tr>
                                <input type="hidden" name="latitude" value="' . $latitude .'">
                                <input type="hidden" name="longitude" value="' . $longitude . '">
                                <input type="hidden" name="familyid" value="' . $family_id . '">
                                <th scope="row">'. $family_id .'</th>
                                <td>'.$num_members.'</td>
                                <td>'.$num_pwd.'</td>
                                ';
                    }
                }
                
            ?>
        </tbody>
    </table>
    <div id="unreg">
        <p>Evacuation Center 3</p>
    </div>

    <table class="table table-hover table-bordered table-light">
        <thead>
            <tr>
                <th scope="col">Family ID#</th> 
                <th scope="col">Number of Members</th>
                <th scope="col">Number of PWD</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
                
                
                $sql = "SELECT * FROM tbl_families WHERE evacID = 4";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)){
                        $family_id = $row['family_id'];
                        $num_members = $row['num_members'];
                        $num_pwd = $row['num_pwd'];
                        $latitude = $row['latitude'];
                        $longitude = $row['longitude'];

                        echo '<tr>
                                <input type="hidden" name="latitude" value="' . $latitude .'">
                                <input type="hidden" name="longitude" value="' . $longitude . '">
                                <input type="hidden" name="familyid" value="' . $family_id . '">
                                <th scope="row">'. $family_id .'</th>
                                <td>'.$num_members.'</td>
                                <td>'.$num_pwd.'</td>
                                ';
                    }
                }
               
            ?>
        </tbody>
    </table>