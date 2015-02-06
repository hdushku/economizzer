<?php
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use yii\bootstrap\Nav;
use yii\data\SqlDataProvider;
use yii\grid\GridView;
/* @var $this yii\web\View */
$this->title = 'Economizzer';
$this->title = Yii::t('app', 'Vistão Geral');
$this->params['breadcrumbs'][] = $this->title;


$thisyear  = date('Y');
$thismonth = date('m');
$lastmonth = date('m', strtotime('-1 months', strtotime(date('Y-m-d'))));
?>
<div class="row">
        <div class="col-xs-6 col-md-3">
            <?php
            echo Nav::widget([
                'items' => [
                    [
                        'label' => 'Resumo do Mês', 'active'=>true,
                        'url' => ['site/index'],
                        'options' => ['class' => 'active','role'=>'presentation'],
                        //'items' => [
                        //     ['label' => 'Semanal', 'url' => '#'],
                        //     ['label' => 'Media', 'url' => '#'],
                        //],
                    ],
                    [
                        'label' => 'Desempenho Anual',
                        'url' => ['site/index'],
                        'options' => ['class' => 'disabled'],
                    ],
                    [
                        'label' => 'Evolução',
                        'url' => ['site/index'],
                        'active' => false,
                        'options' => ['class' => 'disabled'],
                    ],
                    [
                        'label' => 'Top 5',
                        'url' => ['site/index'],
                        'active' => 'false',
                        'options' => ['class' => 'disabled'],
                    ],
                    [
                        'label' => 'Detalhamento',
                        'url' => ['site/index'],
                        'active' => 'true',
                        'options' => ['class' => 'disabled'],
                    ],
                    /*
                    [
                        'label' => 'Dropdown',
                        'items' => [
                             ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
                             '<li class="divider"></li>',
                             '<li class="dropdown-header">Dropdown Header</li>',
                             ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
                        ],
                    ],
                    */
                ],
            ]);
            ?>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-9">
        <h2>
          <span>Resumo do Mês <small><?php echo $thismonth."/".$thisyear ?></small></span>
        </h2>
        <hr/>
            <div class="row">
                  <div class="col-md-6">
                  <div class="panel panel-default">
                <div class="panel-heading"><strong>Receita x Despesa</strong></div>
                  <div class="panel-body">       
                  <?php
          // Via Query Builder
          /*
          $query = (new \yii\db\Query())->from('tb_cashbook');
          $sum = $query->sum('value');
          echo $sum."</br>";
          */
          // Via Data Access Objects
          $command = Yii::$app->db->createCommand("SELECT sum(value) FROM tb_cashbook WHERE type_id = 1 AND MONTH(date) = $thismonth AND YEAR(date) = $thisyear");
          $vtype1 = $command->queryScalar();

          $command = Yii::$app->db->createCommand("SELECT sum(value) FROM tb_cashbook WHERE type_id = 2 AND MONTH(date) = $thismonth AND YEAR(date) = $thisyear");
          $vtype2 = $command->queryScalar();

          // MES ANTERIOR;
          $lastmonth_command = Yii::$app->db->createCommand("SELECT sum(value) FROM tb_cashbook WHERE type_id = 1 AND MONTH(date) = $lastmonth AND YEAR(date) = $thisyear");
          $lastmonth_type1 = $lastmonth_command->queryScalar();

          $lastmonth_command = Yii::$app->db->createCommand("SELECT sum(value) FROM tb_cashbook WHERE type_id = 2 AND MONTH(date) = $lastmonth AND YEAR(date) = $thisyear");
          $lastmonth_type2 = $lastmonth_command->queryScalar();

          function percent($num_amount, $num_total) {
          $count1 = $num_amount / $num_total;
          $count2 = $count1 * 100;
          $count = number_format($count2, 2);
          return $count;
          }

            echo Highcharts::widget([
                'options' => [
                    'plotOptions ' => 'pie',
                    'credits' => ['enabled' => false],
                    'chart'=> [
                    'height'=> 300,
                    ],
                    'title' => ['text' => ''],
                    'colors'=> ['#18bc9c','#e74c3c'],
                    'tooltip'=> ['pointFormat'=> 'Percentual: <b>{point.percentage:.1f}%</b>'],
                    'plotOptions'=> [
                        'pie'=> [
                          'allowPointSelect'=> true,
                          'cursor'=> 'pointer',
                          'dataLabels'=> [
                          'enabled'=> false,
                          ],
                        'showInLegend'=> [
                          'enabled'=> true,
                          ]
                        ]
                    ],
                    'series'=> [[
                        'type'=> 'pie',
                        'name'=> 'Valor',
                        'data'=> [
                            ['Receita',   round((int)$vtype1)],
                            ['Despesa',   abs(round((int)$vtype2))],
                        ]
                    ]]
                ]
                ]);
                ?></div></div></div>
                  <div class="col-md-6">
                      <div class="panel panel-default">
                    <div class="panel-heading"><strong>Desempenho</strong></div>
                    <div class="panel-body">
                    <h4>Seu saldo do mês está: <span class="label label-danger">Negativo</span></h4>
                    <br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Mês Atual</th>
                                <th>Mês Anterior</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Receita</td>
                                <td><?php echo round((int)$vtype1);?></td>
                                <td><?php echo round((int)$lastmonth_type1);?></td>
                                <td><i class="fa fa-arrow-down"></i><?php echo percent(55, 4868);?></td>
                            </tr>
                            <tr>
                                <td>Despesa</td>
                                <td><?php echo abs(round((int)$vtype2));?></td>
                                <td><?php echo abs(round((int)$lastmonth_type2));?></td>
                                <td><i class="fa fa-long-arrow-up"></i> <?php echo percent(1084, 2038);?></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    </div>
                  </div>
            </div>
            
            </div>
        </div>
 </div>