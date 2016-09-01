<?php

echo \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'id',
                'Name',
                'Format',
                'category',
                'Publisher'
            ]
        ]);