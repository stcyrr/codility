// you can write to stdout for debugging purposes, e.g.
// print "this is a debug message\n";

function solution($A, $B, $C) {
    // write your code in PHP5.5
    
    $nb = count($A);
    
    $nb_son = array_fill(0, $nb, 0);
    
    for ($i = $nb - 1; $i >=0; $i--)
    {
        $x = $C[$i];
        if ($x != -1) 
        {
            $nb_son[$x] ++;
        }    
    }
    
    
    for ($i = 0; $i < $nb; $i++)
    {
        $parent = $C[$i];
        if ($parent != -1)
        {
            if ($nb_son[$parent] == 1)
            {
                // parent has only one son... adjust current node
                $A[$i] = min($A[$parent] - $B[$parent], $A[$i]);
                $C[$i] = $C[$parent];
            }
        }
            
            
        if ($B[$i] > $A[$i])
        {
            return $i;
        }
        
        $w = $B[$i];
        $current = $C[$i];
        while ($current != -1)
        {
            $B[$current] += $w;
            if ($B[$current] > $A[$current])
            {
                return $i;
            }
            $current = $C[$current];
        }
    }    
    return $nb;
}

