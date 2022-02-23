<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\CollectionTreeBuilder;

class TreeBasedModel extends Model
{
    protected static string $treeIdKey = 'id';
    protected static string $treeParentKey = 'parent_id';
    protected static string $treeChildrenKey = 'children';
    private static bool $isDeleting = false;

    public static function tree(int $findNodeId = null)
    {
        $nodes = static::get()->keyBy(static::$treeIdKey);
        $nodes = self::treeFillChildren($nodes);

        if ($findNodeId) {
            return $nodes[$findNodeId] ?? null;
        }

        $nodes = self::treeRemoveExcessNodes($nodes);

        return $nodes->values();
    }

    private static function treeFillChildren(Collection $nodes): Collection //: void
    {
        foreach ($nodes as $node) {
            if ($node->{static::$treeParentKey}) {
                $parent = $nodes[$node->{static::$treeParentKey}];
                $parent->treeAddChildren($node);
            }
        }

        return $nodes;
    }

    private static function treeRemoveExcessNodes(Collection $nodes): Collection //: void
    {
        foreach ($nodes as $node) {
            if ($node->{static::$treeParentKey}) {
                unset($nodes[$node->id]);
            }
        }

        return $nodes;
    }

    public function treeAddChildren(self $node): void
    {
        if (!isset($this->attributes[static::$treeChildrenKey])) {
            $this->attributes[static::$treeChildrenKey] = [];
        }

        $this->attributes[static::$treeChildrenKey][] = $node;
    }

    public function treeCollectIds(&$ids = [])
    {
        if ($this->{static::$treeChildrenKey}) {
            foreach ($this->{static::$treeChildrenKey} as $child) {
                $child->treeCollectIds($ids);
            }
        }

        $ids[] = $this->{static::$treeIdKey};
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (self $node) {
            if (self::$isDeleting) return;

            self::$isDeleting = true;
            $cascadeIds = [];
            self::tree($node->id)->treeCollectIds($cascadeIds);
            self::destroy($cascadeIds);
            self::$isDeleting = false;
        });
    }
}
