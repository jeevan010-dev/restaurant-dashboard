<?php
$restaurants=[['id'=>101,'name'=>'Pizza Hub','cuisine'=>'Italian'],
              ['id'=>102,'name'=>'Curry King','cuisine'=>'Indian'],
              ['id'=>103,'name'=>'Sushi Bar','cuisine'=>'Japanese'],
              ['id'=>104,'name'=>'Salad Stop','cuisine'=>'Healthy']];
file_put_contents(__DIR__.'/../data/restaurants.json',json_encode($restaurants,JSON_PRETTY_PRINT));

$orders=[]; $id=1; $start=new DateTimeImmutable('2025-06-20');
for($d=0;$d<7;$d++){
  for($i=0;$i<30;$i++){
    $dt=$start->modify("+$d days")->setTime(rand(9,21),rand(0,59));
    $orders[]=['id'=>$id++,'restaurant_id'=>$restaurants[array_rand($restaurants)]['id'],
      'order_amount'=>rand(100,1000),'order_time'=>$dt->format('Y-m-d\TH:i:s')];
  }
}
file_put_contents(__DIR__.'/../data/orders.json',json_encode($orders,JSON_PRETTY_PRINT));
echo "Data generated.\n";
