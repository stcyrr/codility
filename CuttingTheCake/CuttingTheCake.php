<?php

/*
 * Finnd how many numbers are >= to K. For each row, find how many columns are >=.
 * Start at index 0 and increment until we find one entry >=. The remaining entries will also be >= because
 * of the way they are sorted.
 */
function greater_eq(&$row, &$col, $N, $K)
{
    global $dbg_trace;

    if ($dbg_trace)
    {
        printf ("Looking for values >= %d\n", $K);
    }

    $result = 0;
    $c = $N - 1;
    for ($r = 0; $r < $N; $r ++)
    {
        while (($c >= 0) && (($row[$r] * $col[$c]) >= $K))
        {
            $c--;
        }
        $result += $N - $c - 1;
    }
    return $result;
}



function compute_values(&$result, &$V, $N)
{
    global $dbg_trace;

    if ($dbg_trace)
    {
        printf("Computing valudes with N=%d\n", $N);
        print_r($V);
    }

    $nb = count($V);
    $result [0] = $V[0];
    for ($i = 1; $i < $nb; $i++)
    {
        $result[$i] = $V[$i] - $V[$i-1];
    }

    $result[$nb] = $N - $V[$nb-1];

    sort($result);
}

function solution($X, $Y, $K, $A, $B)
{
    global $dbg_trace;

    $dbg_trace = 0;

    $row = array();
    $col = array();

    compute_values($row, $A, $X);
    compute_values($col, $B, $Y);

    if ($dbg_trace)
    {
        print_r($row);
        print_r($col);
    }
    $grid_size = count($row);

    // Search a value for which the number of values >= is K

    $start = 1;
    $end = $row[$grid_size-1] * $col[$grid_size-1];
    $result = 0;
    while ($start <= $end)
    {
        $mid = intval(($start + $end) / 2);
        if (greater_eq($row, $col, $grid_size, $mid) >= $K)
        {
            $start = $mid + 1;
            $result = $mid;
        }
        else {
            $end = $mid - 1;
        }
    }

    return $result;

}

$X1 = 6;
$Y1 = 7;
$K1 = 3;
$A1 = array(1, 3);
$B1 = array (1, 5);

$X2 = 2;
$Y2 = 2;
$K2 = 2;
$A2 = array(1);
$B2 = array (1);

// printf ("Result = %d\n", solution($X1, $Y1, $K1, $A1, $B1));
printf ("Result = %d\n", solution($X2, $Y2, $K2, $A2, $B2));