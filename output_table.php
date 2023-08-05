<?php

    function output_table($data = array(), $selectable) 
    {
        $rows = array();
        $keys = array_keys($data[0]);
        if ($selectable)
        {
            $header = "<tr><th></th><th>" . implode("</th><th>", $keys) . "</th></tr>";
        } else {
            $header = "<tr><th>" . implode("</th><th>", $keys) . "</th></tr>";
        }

        foreach ($data as $row) {
            $cells = array();
            $ids = array();
            foreach ($row as $cell) {
                $cells[] = "<td>{$cell}</td>";
                $ids[] = $cell;
            }
            $checkbox = "";
            if ($selectable)
            {
                $checkbox = "<td><input type='checkbox' name='selectedids[]' value='".$ids[0]."' ></td>";
            }
            array_unshift($cells, "");
            $rows[] = "<tr>".$checkbox.implode('', $cells) . "</tr>";
        }
        return "<table class='table table-striped table-bordered'>" . $header . implode('', $rows) . "</table>";
    }
    
?>