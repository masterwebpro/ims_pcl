<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE VIEW available_items AS SELECT
        masterfiles.masterfile_id,
        rh.rcv_no,
        rh.date_received,
        masterfiles.customer_id,
        masterfiles.company_id,
        cl.client_name as customer_name,
        com.client_name as company_name,
        masterfiles.store_id,
        store_name,
        masterfiles.warehouse_id,
        w.warehouse_name,
        masterfiles.product_id,
        products.product_name,
        products.product_code,
        products.product_sku,
        products.is_serialize,
        masterfiles.item_type,
        sl.location,
        masterfiles.storage_location_id,
        masterfiles.status,
        sum(masterfiles.inv_qty) as inv_qty,
        masterfiles.inv_uom,
        u.code as inv_uom_code,
        rd.lot_no,
        rd.expiry_date
        FROM `masterfiles`
        left join products on products.product_id = masterfiles.product_id
        left join storage_locations sl on sl.storage_location_id = masterfiles.storage_location_id
        left join client_list cl on cl.id = masterfiles.customer_id
        left join client_list com on com.id = masterfiles.company_id
        left join store_list s on s.id = masterfiles.store_id
        left join warehouses w on w.id = masterfiles.warehouse_id
        left join rcv_hdr rh on rh.rcv_no = masterfiles.ref_no
        left join rcv_dtl rd on rd.rcv_no = masterfiles.ref_no
        left join uom u on u.uom_id = masterfiles.inv_uom
        group by masterfiles.ref_no, cl.client_name, store_name, w.warehouse_name, product_name, sl.location, masterfiles.status, masterfiles.inv_uom,rd.lot_no,rd.expiry_date
        having inv_qty > 0
        order by date_received, product_name ASC');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS available_items;');
    }
};
