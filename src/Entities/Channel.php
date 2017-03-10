<?php declare(strict_types=1);

namespace FondBot\Database\Entities;

use Carbon\Carbon;
use FondBot\Entities\AbstractEntity;

/**
 * @property int $id
 * @property string $driver
 * @property string $name
 * @property array $parameters
 * @property bool $is_enabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Channel extends AbstractEntity
{

    protected $table = 'channels';

    protected $visible = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'parameters' => 'array',
    ];

    protected $fillable = [
        'name',
        'driver',
        'parameters',
        'is_enabled',
    ];

}