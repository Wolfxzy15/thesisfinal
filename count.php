<?php

$counta1 = "SELECT COUNT(*) AS count1
            FROM tbl_residents
            INNER JOIN tbl_families ON tbl_residents.family_id = tbl_families.family_id
            WHERE tbl_families.evacid = '2';  -- Replace 'E1' with the desired evac_id
        ";
        $countb1 = mysqli_query($conn, $counta1);
        if ($countb1) {
            $countc1 = mysqli_fetch_assoc($countb1);}


$counta2 = "SELECT COUNT(*) AS count2
            FROM tbl_residents
            INNER JOIN tbl_families ON tbl_residents.family_id = tbl_families.family_id
            WHERE tbl_families.evacid = '3';  -- Replace 'E1' with the desired evac_id
        ";
        $countb2 = mysqli_query($conn, $counta2);
        if ($countb2) {
            $countc2 = mysqli_fetch_assoc($countb2);}


$counta3 = "SELECT COUNT(*) AS count3
            FROM tbl_residents
            INNER JOIN tbl_families ON tbl_residents.family_id = tbl_families.family_id
            WHERE tbl_families.evacid = '4';  -- Replace 'E1' with the desired evac_id
        ";
        $countb3 = mysqli_query($conn, $counta3);
        if ($countb3) {
            $countc3 = mysqli_fetch_assoc($countb3);}
?>