<?php

namespace App\Models;

use App\Helpers\Nestedset\NodeTrait;
use App\Models\Traits\HasContent;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use
        // LogsActivity, // Application not ready for this yet.
        NodeTrait,
        HasContent;

    public $contentField = 'body';

    protected $guarded = [];

    /**
     * Determine if a comment has child comments.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the commentable entity that the comment belongs to.
     *
     * @return mixed
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function creator(): MorphTo
    {
        return $this->morphTo('creator');
    }

    /**
     * Create a comment and persists it.
     *
     * @param Model $commentable
     * @param array $data
     * @param Model $creator
     *
     * @return static
     */
    public function createComment(Model $commentable, array $data, Model $creator): self
    {
        return $commentable->comments()->create(array_merge($data, [
            'creator_id'   => $creator->getAuthIdentifier(),
            'creator_type' => $creator->getMorphClass(),
        ]));
    }

    /**
     * Update a comment by an ID.
     *
     * @param int   $id
     * @param array $data
     *
     * @return bool
     */
    public function updateComment(int $id, array $data): bool
    {
        return (bool) static::find($id)->update($data);
    }

    public function comment($data, $creator, $parent = null)
    {
        $commentableModel = static::class;

        $comment = (new $commentableModel())->createComment($this, $data, $creator);

        if (!empty($parent)) {
            $parent->appendNode($comment);
        }

        return $comment;
    }

    /**
     * Delete a comment by an ID.
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteComment(int $id): bool
    {
        return (bool) static::find($id)->delete();
    }

    public function comments()
    {
        return $this->morphMany(static::class, 'commentable');
    }

    public function getUrl()
    {
        return route('comment.show', ['id' => $this->id]);
    }
}
