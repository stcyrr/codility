<?php

/*
 * This is a solution to the PrisonEscape challenge on codility. It runs with a result of 100%.
 * 
 * The approach is to build a tree representing the prison and to traverse that tree depth first. 
 * Such a traversal is O(n). When traversing the tree we keep track of which node (prison cell) 
 *  
 */
class Node {
    /*
     * hasAccessToLeaf indicates that the subtree has access to an unguarded exit towards the leaf of the tree
     * hasPrisonerToRoot indicates that the subtree has prisoner that are not guarded to the root
     */
    var $parentIndex;
    var $isPrisoner;
    var $hasAccessToLeaf;
    var $hasPrisonerToRoot;
    var $tList = array();

    function __construct()
    {
        $this->parentIndex = 0;
        $this->isPrisoner = 0;
        $this->hasAccessToLeaf = 0;
        $this->hasPrisonerToRoot = 0;
    }

    public function isLeaf()
    {
        return count($this->tList) <= 1;
    }

    public function add_elem($e)
    {
        array_push($this->tList, $e);
    }
}

class Stack {
    private $nb_elem;
    private $data = array();

    function __construct()
    {
        $this->nb_elem = 0;
    }

    public function push($d)
    {
        $this->date[$this->nb_elem] = $d;
        $this->nb_elem++;
    }

    public function pop()
    {
        if ($this->nb_elem > 0)
        {
            $this->nb_elem--;
            return $this->date[$this->nb_elem];
        }
        return 0;
    }


    public function is_empty()
    {
        return $this->nb_elem == 0;
    }
}



function dfs (&$nodes, &$visited, &$res, $nodeIndex)
{
    global $dbgTrace;

    $s1 = new Stack();
    $s2 = new Stack();

    $s1->push($nodeIndex);
    while (!$s1->is_empty())
    {
        $nodeIndex = $s1->pop();

        if ($visited[$nodeIndex] == FALSE)
        {
            $node = $nodes[$nodeIndex];
            $visited[$nodeIndex] = TRUE;
            $s2->push($nodeIndex);

            $nb = count($node->tList);
            for ($i = 0; $i < $nb; $i++)
            {
                $nextNodeIndex = $node->tList[$i];
                if ($visited[$nextNodeIndex] == FALSE)
                {
                    $nodes[$nextNodeIndex]->parentIndex = $nodeIndex;
                    $s1->push($nextNodeIndex);
                }
            }
        }
    }




    while (!$s2->is_empty())
    {

        $nodeIndex = $s2->pop();

        if ($dbgTrace)
        {
            printf ("+++ dfs %d\n", $nodeIndex);
        }

        $node = $nodes[$nodeIndex];

        $nb = count($node->tList);
        for ($i = 0; $i < $nb; $i++)
        {
            $nextNodeIndex = $node->tList[$i];
            if ($visited[$nextNodeIndex] == FALSE)
            {
                $nodes[$nextNodeIndex]->parentIndex = $nodeIndex;
                dfs($nodes, $visited, $res, $nextNodeIndex);
            }
        }

        $n = 0;
        $accessLeaf = 0;
        $prisonerToRoot = 0;

        if ($nodeIndex != $node->parentIndex && $node->isLeaf())
        {
            /*
             * The node is a leaf... so it has access to an unguarded exit
             */
            $node->hasAccessToLeaf = TRUE;

            if ($dbgTrace)
            {
                printf ("Node %d is a leaf\n", $nodeIndex);
            }
        }
        else
        {
            if ($dbgTrace)
            {
                printf ("Node %d is NOT a leaf\n", $nodeIndex);
            }



            /*
             * Let's count how many subtree have access to an unguarded exit,
             * and how many subtrees contain prisoners that can escape through root (i.e. this node)
             */
            for ($i = 0; $i < count($node->tList); $i++)
            {
                $next = $node->tList[$i];
                if ($node->parentIndex != $next)
                {
                    $n++;
                    if ($nodes[$next]->hasAccessToLeaf)
                    {
                        $accessLeaf++;
                    }
                }

                if ($nodes[$next]->hasPrisonerToRoot)
                {
                    $prisonerToRoot++;
                }
            }


            if ($node->isPrisoner)
            {
                /*
                 * The node contains a prisoner. Block all access path to exits through leaf and
                 * state that the current subtree does contain prisoner that can exit through root.
                 *
                 * At this point, we can assume that the current subtree can't exit throug a leaf
                 * because we just blocked those exits.
                 */
                if ($dbgTrace)
                {
                    printf ("Node is prisoner, must guard %d leaf\n", $accessLeaf);
                }
                $node->hasAccessToLeaf = 0;
                $node->hasPrisonerToRoot = 1;
                $res += $accessLeaf;
            }

            else
            {
                if ($dbgTrace)
                {
                    printf ("In else %d %d\n", $accessLeaf, $n);
                }

                if ($accessLeaf == 0)
                {
                    /*
                     * The current subtree can't exit through a leaf. So, we don't need to put a guard.
                     */

                    $node->hasAccessToLeaf = 0; // set no escape to leaf
                    if ($prisonerToRoot > 0)
                    { // if at least one subtree has prisoner can escape to root
                        $node->hasPrisonerToRoot = TRUE;
                    }
                }
                else
                {
                    /*
                     * There are some exits through leafs in the subtree of this node...
                     *
                     * If no subtrees contain prisonner that can exit through root, we just take note of the access.
                     * Otherwise, we need to put a guard here to ensure that those prisonners
                     * can't use one of the sub-trees
                     */

                    if ($prisonerToRoot == 0)
                    {
                        $node->hasAccessToLeaf = 1;
                        $node->hasPrisonerToRoot = 0;
                    } else
                    {
                        $res++;
                        $node->hasAccessToLeaf = 0;
                        $node->hasPrisonerToRoot = 0;
                    }
                }
            }
        }
        if ($dbgTrace)
        {
            printf ("n: %d, leaf: %d, root: %d res: %d\n", $n, $accessLeaf, $prisonerToRoot, $res);
            printf ("--- dfs %d\n", $nodeIndex);
        }
    }
}



function solution($A, $B, $C) {
    // write your code in PHP5.5

    global $dbgTrace;

    $dbgTrace= 0;
    $nb_nodes = count($A) + 1;
    $nb_links = count($A);
    $nb_prisoners = count($C);

    if ($nb_prisoners == 0)
    {
        return 0;
    }

    $res = 0;
    $visited = array_fill(0, $nb_nodes, 0);
    $nodes = array();
    for ($i = 0; $i < $nb_nodes; $i++)
    {
        $nodes[$i] = new Node();
    }

    for ($i = 0; $i < $nb_links; $i++) {
        $x = $A[$i];
        $y = $B[$i];

        $nodes[$x]->add_elem($y);
        $nodes[$y]->add_elem($x);
    }


    for ($i = 0; $i < $nb_prisoners; $i++) {
        $x = $C[$i];

        if ($nodes[$x]->isLeaf())
        {
            if ($dbgTrace)
            {
                printf ("Returning because of node %d\n", $x);
            }
            return -1;
        }
        $nodes[$x]->isPrisoner = 1;
    }

    if ($dbgTrace)
    {
        printf ("%d %d %d\n", $nb_nodes, $nb_links, $nb_prisoners);
    }

    dfs($nodes, $visited, $res, 0);


    if ($dbgTrace)
    {
        print_r($visited);
        print_r($nodes);
    }

    return $nodes[0]->hasPrisonerToRoot && count($nodes[0]->tList) == 1 ? $res + 1 : $res;

}

$A1 = array (0, 1, 2, 2, 4, 4, 5, 7, 7, 8, 10, 10, 11, 13, 13, 15);
$B1 = array (1, 2, 3, 4, 5, 7, 6, 8, 10, 9, 11, 13, 12, 14, 15, 16);
$C1 = array (4, 7);


$A2 = array (0, 1, 2, 3, 3, 2, 6, 6);
$B2 = array (1, 2, 3, 4, 5, 6, 8, 7);
$C2 = array (1, 6);


$A3 = array (0);
$B3 = array (1);
$C3 = array (1);

    error_reporting(~0);
    $res1 = Solution($A2, $B2, $C2);
    printf ("Result is %d\n", $res1);









?>