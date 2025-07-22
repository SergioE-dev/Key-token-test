<?php
use yii\db\Migration;

class m20250627_143200_crear_tabla_reservas extends Migration
{
    public function safeUp()
    {
        // Tabla vehículos
        $this->createTable('vehiculos', [
            'id' => $this->primaryKey(),
            'modelo' => $this->string(100)->notNull(),
            'marca' => $this->string(50)->notNull(),
            'autonomia' => $this->integer(),
            'precio_hora' => $this->decimal(10,2),
            'imagen' => $this->string(255),
            'disponible' => $this->boolean()->defaultValue(true),
        ]);

        // Insertar datos iniciales
        $this->batchInsert('vehiculos', 
            ['modelo', 'marca', 'autonomia', 'precio_hora', 'imagen'],
            [
                ['Model S', 'Tesla', 650, 12000, 'tesla-model-s.jpg'],
                ['Ioniq 5', 'Hyundai', 480, 9800, 'ioniq5.jpg'],
                ['Bolt EV', 'Chevrolet', 416, 8500, 'bolt-ev.jpg'],
                ['e-208', 'Peugeot', 340, 7900, 'e208.jpg'],
            ]
        );

        // Tabla reservas
        $this->createTable('reservas', [
            'id' => $this->primaryKey(),
            'nombre' => $this->string(50)->notNull(),
            'apellido' => $this->string(50)->notNull(),
            'rut' => $this->string(15)->notNull(),
            'email' => $this->string(100)->notNull(),
            'telefono' => $this->string(15)->notNull(),
            'vehiculo_id' => $this->integer()->notNull(),
            'fecha_reserva' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-reserva-vehiculo',
            'reservas',
            'vehiculo_id',
            'vehiculos',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-reserva-vehiculo', 'reservas');
        $this->dropTable('reservas');
        $this->dropTable('vehiculos');
    }
}