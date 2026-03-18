<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'share_url_pattern',
        'color',
        'open_in_new_tab',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function blogPosts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_social_link')
            ->withPivot('custom_url');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Build the share URL for a given blog post URL.
     * Replaces {url}, {title}, and {excerpt} placeholders.
     */
    public function buildShareUrl(string $postUrl, string $title = '', string $excerpt = ''): string
    {
        if (empty($this->share_url_pattern)) {
            return $postUrl;
        }

        return str_replace(
            ['{url}', '{title}', '{excerpt}'],
            [urlencode($postUrl), urlencode($title), urlencode($excerpt)],
            $this->share_url_pattern
        );
    }
}
