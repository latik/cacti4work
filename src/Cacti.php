<?php

class Cacti
{
    public static $cacti_cli_path = '/usr/local/www/cacti/cli/';

    /*
        add_device.php  --list-host-templates
    */
    public static function addHost($host_template, $ip, $community, $name = null)
    {
        $add_device_cmd = self::$cacti_cli_path . "add_device.php";
        $add_device_cmd .= " --template={$host_template}";
        $add_device_cmd .= " --community={$community}";
        $add_device_cmd .= " --description='{$name}'";
        $add_device_cmd .= " --ping_method='udp'";
        $add_device_cmd .= " --ip={$ip}";

        $res = shell_exec(escapeshellcmd("/usr/local/bin/php {$add_device_cmd}"));
        //var_dump($res);
        if (1 === preg_match('#device-id: \((\d+)\)#usi', $res, $matches)) {
            $host_id = $matches[1];
            return $host_id;
        }
        return false;
    }

    /*
    add_graphs.php --list-snmp-queries
    add_graphs.php --snmp-query-id=1 --list-query-types
    add_graphs.php --host-id=2 --list-snmp-fields
    add_graphs.php --host-id=2 --snmp-field=ifType --list-snmp-values
    */
    public static function addGraphs4Host($host_id, $graph_template)
    {
        $graph_ids = [];
        $add_graphs_cmd = self::$cacti_cli_path . "add_graphs.php";
        $add_graphs_cmd .= " --host-id={$host_id}";
        $add_graphs_cmd .= " --graph-template-id={$graph_template['id']}";
        $add_graphs_cmd .= " --snmp-query-id={$graph_template['query_id']}";
        $add_graphs_cmd .= " --graph-type=ds";
        $add_graphs_cmd .= " --snmp-query-type-id={$graph_template['query_type']}";
        $add_graphs_cmd .= " --snmp-field={$graph_template['snmp_field']}";
        $add_graphs_cmd .= " --snmp-value={$graph_template['snmp_value']}";

        $res = shell_exec(escapeshellcmd("/usr/local/bin/php {$add_graphs_cmd}"));
        //var_dump($res);
        if (preg_match_all('#graph-id: \((\d+)\)#usi', $res, $matches)) {
            $graph_ids = $matches[1];
        }
        return $graph_ids;
    }


    /*
    */
    public static function addHeader2Tree($name, $parent_node = null, $tree = 1)
    {
        $cmd = self::$cacti_cli_path . "add_tree.php";
        $cmd .= " --type=node";
        $cmd .= " --tree-id={$tree}";
        if (!empty($parent_node)) {
            $cmd .= " --parent-node={$parent_node}";
        }
        $cmd .= " --node-type=header";
        $cmd .= " --name='{$name}'";
        $res = shell_exec(escapeshellcmd("/usr/local/bin/php $cmd"));

        //var_dump($res);
        if (1 === preg_match('#node-id: \((\d+)\)#usi', $res, $matches)) {
            $node_id = $matches[1];
            return $node_id;
        }
    }

    /*
    */
    public static function addGraphs2Tree(array $graph_ids, $parent_node, $tree = 1)
    {
        $node_id = [];
        //var_dump($graph_ids);
        foreach ($graph_ids as $graph) {
            $add_graphs_cmd = self::$cacti_cli_path . "add_tree.php";
            $add_graphs_cmd .= " --type=node";
            $add_graphs_cmd .= " --node-type=graph";
            $add_graphs_cmd .= " --tree-id=" . $tree;
            $add_graphs_cmd .= " --parent-node=" . $parent_node;
            $add_graphs_cmd .= " --graph-id=" . $graph;

            //echo $add_graphs_cmd, PHP_EOL;
            $res = shell_exec(escapeshellcmd("/usr/local/bin/php $add_graphs_cmd"));
            //var_dump($res);
            if (1 === preg_match('#node-id: \((\d+)\)#usi', $res, $matches)) {
                $node_id[] = $matches[1];
            }
        }
        return $node_id;
    }

    /*
*/
    public static function addHostGraphs2Tree($host_id, $parent_node, $tree = 1)
    {
        $node_id = [];
        //var_dump($graph_ids);
            $add_graphs_cmd = self::$cacti_cli_path . "add_tree.php";
            $add_graphs_cmd .= " --type=node";
            $add_graphs_cmd .= " --node-type=host";
            $add_graphs_cmd .= " --tree-id=" . $tree;
            $add_graphs_cmd .= " --parent-node=" . $parent_node;
            $add_graphs_cmd .= " --host-id=" . $host_id;

            //echo $add_graphs_cmd, PHP_EOL;
            $res = shell_exec(escapeshellcmd("/usr/local/bin/php $add_graphs_cmd"));
            //var_dump($res);

            if (1 === preg_match('#node-id: \((\d+)\)#usi', $res, $matches)) {
                $node_id[] = $matches[1];
            }

        return $node_id;
    }
}
