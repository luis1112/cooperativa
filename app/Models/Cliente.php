<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model{

    protected $table = 'modelo_cliente';

    protected $fillable = [
        'cedula','nombres','apellidos','genero','estadoCivil','correo','telefono','celular','direccion','date_created','updated_at'
    ];

    public $timestamps = false; //no se guarda los valores del create_at y update_at

    protected $hidden =[
        'cliente_id'
    ];
}