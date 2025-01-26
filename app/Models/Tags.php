<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tags extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function IsNegativeTag($tag)
    {
        return Str::contains($tag, '-');
    }

    public function images() : BelongsToMany
    {
        return $this->belongsToMany(Image::class);
    }

    public static function sortTags(Builder $query, ?string $tags) : Builder
    {
        if(empty($tags)) return $query;

        $tagList = explode(' ', $tags);

        $negativeTags = [];
        $positiveTags = [];

        foreach ($tagList as $tag)
        {
            if(Tags::IsNegativeTag($tag))
            {
                $tag = str::after($tag, '-');
                array_push($negativeTags, $tag);
            }
            else
            {
                array_push($positiveTags, $tag);
            }
        }

        if(count($negativeTags) > 0)
        {
            $query->whereDoesntHave('tags', function (Builder $query) use($negativeTags)
            {
                $first = true;

                foreach($negativeTags as $tag)
                {
                    if($first)
                    {
                        $query->where('name', 'like', '%'.$tag.'%');
                        $first = false;
                    }
                    else
                    {
                        $query->orWhere('name', 'like', '%'.$tag.'%');
                    }
                }
            });
        }

        if(count($positiveTags) > 0)
        {
            $query->WhereHas('tags', function (Builder $query) use ($positiveTags)
            {
                $first = true;

                foreach ($positiveTags as $tag)
                {
                    if($first)
                    {
                        $query->where('name', 'like', '%'.$tag.'%');
                        $first = false;
                    }
                    else
                    {
                        $query->orWhere('name', 'like', '%'.$tag.'%');
                    }
                }
            });
        }
        else {
            $query->orWhereDoesntHave('tags');
        }

        return $query;
    }
}
