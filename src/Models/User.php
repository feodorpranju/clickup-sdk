<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\Http\ApiClientInterface;
use Feodorpranju\ApiOrm\Support\ModelCollection;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasAfterSelectFilter;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

/**
 * @method static withClient(ApiClientInterface $client)
 */
class User extends AbstractModel
{
    use HasAfterSelectFilter;

    public const ENTITY_NAME = 'user';

    public function __construct(array|Collection $attributes = [])
    {
        $attributes = collect($attributes);

        if ($attributes->has('user') && is_array($attributes->get('user'))) {
            $attributes = $attributes->merge($attributes->get('user'));
            $attributes->forget('user');
        }

        if ($attributes->has('invited_by')) {
            $attributes->put('invited_by', static::make($attributes->get('invited_by')));
        }

        parent::__construct($attributes);
    }

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::Int),
            'username' => new FieldSettings('username', FieldType::String),
            'email' => new FieldSettings('email', FieldType::Email),
            'color' => new FieldSettings('color', FieldType::String),
            'profilePicture' => new FieldSettings('profilePicture', FieldType::Url),
            'initials' => new FieldSettings('initials', FieldType::Int),
            'role' => new FieldSettings('role', FieldType::String),
            'custom_role' => new FieldSettings('custom_role', FieldType::String),
            'last_active' => new FieldSettings('last_active', FieldType::Datetime),
            'date_joined' => new FieldSettings('date_joined', FieldType::Datetime),
            'date_invited' => new FieldSettings('date_invited', FieldType::Datetime),
            'invited_by' => new FieldSettings('invited_by', FieldType::Cast),
        ]);
    }

    /**
     * Gets user.
     * Detailed user info is available only on enterprise.
     *
     * @param int|string $id
     * @param int|null $workspaceId To get detailed info (Enterprise use only)
     * @param bool $detailedOnly Force detailed info if $workspaceId not set
     * @return $this|null
     */
    public function get(int|string $id, ?int $workspaceId = null, bool $detailedOnly = false): ?static
    {
        if ($workspaceId) {
            static::put(
                static::cmd("team/$workspaceId/user/$id", 'get')
                    ->call()
                    ->getResult()
            );
            $this->updatedFields = [];

            return $this;
        } else {
            $user = static::where('id', $id)->first();

            if ($detailedOnly) {
                return static::get($id, $user->workspace_id);
            }

            return $user;
        }
    }

    public function all(): ModelCollection
    {
        $uerGroups = Workspace::withClient($this->getClient())
            ->all()
            ->map(
                fn($workspace) => $workspace->members
            );

        $users = ModelCollection::make();

        foreach ($uerGroups as $workspaceId => $group) {
            $users = $users->merge($group->values());
        }

        return $users;
    }
}