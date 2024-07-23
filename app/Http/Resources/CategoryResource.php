<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => [
                'tm' => $this->name_tm,
                'en' => $this->name_en,
                'ru' => $this->name_ru,
            ],
            'image' => $this->image ? asset($this->image) : null,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'children_categories' => CategoryResource::collection($this->whenLoaded('childrenCategories')),
            'top_parent' => $this->getTopParent(),
        ];
    }
}