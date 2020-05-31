<?php
namespace inv;

include'vendor/autoload.php';
include'config.php';
$db = \atk4\data\Persistence::connect($c[0],$c[1],$c[2]);

try {
    $c = new Client($db);
    $c->save(['Pear Company '.rand(1,100)]);

    $o = $c->ref('Invoice')
        ->save(['ref'=>'TBL1'])
        ->ref('Line')->import([
            ['table', 'qty'=>2, 'price'=>10.50],
            ['chair', 'qty'=>10, 'price'=>3.25],
        ]);

    $o = $c->ref('Invoice')
        ->save(['ref'=>'CHR2'])
        ->ref('Line')->import([
            ['chair', 'qty'=>rand(1,3), 'price'=>3.25],
            ['book', 'qty'=>rand(2,3), 'price'=>2.00],
        ]);


    $ch = $c->ref('Invoice')->ref('Line')
        ->addCondition('item', 'chair');


    echo "The new client [".$c['name']."] has ordered ".
        $ch->action('fx', ['sum', 'qty']).
        " chairs for a total of ".
        $ch->action('fx', ['sum', 'total']).
        "\n";

    $c->unload();

    echo "In total there are  ".
        $c->action('count').
        " clients, ".
        $c->ref('Invoice')->ref('Line')->action('fx', ['sum', 'qty']).
        " items for a total price of ".
        $c->action('fx', ['sum', 'total']).
        "\n";

    $today = [];
    foreach($c->ref('Invoice')->addCondition('created', new \DateTime()) as $i) {
        $today[] = "   ".$i['name'].' ('.$i['total'].')';
    }
    echo "Invoices added today: \n".join("\n", $today)."\n";


} catch (\atk4\core\Exception $e) {
    echo $e->getColorfulText();
}
