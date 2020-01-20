<?php

namespace App\Actions;

use App\Entities\Entity;

/**
 * Class CommentRepo
 * @package App\Repos
 */
class CommentRepo
{

    /**
     * @var \App\Actions\Comment $comment
     */
    protected $comment;

    /**
     * CommentRepo constructor.
     * @param \App\Actions\Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get a comment by ID.
     * @param $id
     * @return \App\Actions\Comment|\Illuminate\Database\Eloquent\Model
     */
    public function getById($id)
    {
        return $this->comment->newQuery()->findOrFail($id);
    }

    /**
     * Create a new comment on an entity.
     * @param \App\Entities\Entity $entity
     * @param array $data
     * @return \App\Actions\Comment
     */
    public function create(Entity $entity, $data = [])
    {
        $userId = user()->id;
        $comment = $this->comment->newInstance($data);
        $comment->created_by = $userId;
        $comment->updated_by = $userId;
        $comment->local_id = $this->getNextLocalId($entity);
        $entity->comments()->save($comment);
        return $comment;
    }

    /**
     * Update an existing comment.
     * @param \App\Actions\Comment $comment
     * @param array $input
     * @return mixed
     */
    public function update($comment, $input)
    {
        $comment->updated_by = user()->id;
        $comment->update($input);
        return $comment;
    }

    /**
     * Delete a comment from the system.
     * @param \App\Actions\Comment $comment
     * @return mixed
     */
    public function delete($comment)
    {
        return $comment->delete();
    }

    /**
     * Get the next local ID relative to the linked entity.
     * @param \App\Entities\Entity $entity
     * @return int
     */
    protected function getNextLocalId(Entity $entity)
    {
        $comments = $entity->comments(false)->orderBy('local_id', 'desc')->first();
        if ($comments === null) {
            return 1;
        }
        return $comments->local_id + 1;
    }
}
