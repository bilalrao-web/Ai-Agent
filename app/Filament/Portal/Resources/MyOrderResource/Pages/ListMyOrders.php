<?php

namespace App\Filament\Portal\Resources\MyOrderResource\Pages;

use App\Filament\Portal\Resources\MyOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListMyOrders extends ListRecords
{
    protected static string $resource = MyOrderResource::class;
}
