<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {


        $type = null;
        if ($this->webinar_id) {
            $type = 'webinar';
        } elseif ($this->bundle_id) {
            $type = 'bundle';
        } elseif ($this->reserve_meeting_id) {
            $type = 'meeting';
        }
        $info = $this->getItemInfo();
        return [
            'id' => $this->id,
            'webinar_id' => $this->webinar_id,
            'type' => $type,
            'image' => url($info['imgPath']) ?? null,
            'title' => $info['title'] ?? null,
            'teacher_name' => $info['teacherName'] ?? null,
            'rate' => $info['rate'] ?? null,
            'price' => $info['price'] ?? null,
            'discount' => $info['discountPrice'] ?? null,
            'quantity' => $info['quantity'] ?? null,
            $this->mergeWhen($this->reserve_meeting_id, function ()  {
                $time_exploded = explode('-', $this->reserveMeeting->meetingTime->time);
                return [
                    'day' => $this->reserveMeeting->day,
                    //  'time' => $this->reserveMeeting->meetingTime->time,
                    'time' => [
                        'start' => $time_exploded[0],
                        'end' => $time_exploded[1],
                    ],
                    'timezone' => $this->reserveMeeting->meeting->getTimezone()
                ];
            }),
            'batch_id' => $this->batch_id,
            $this->mergeWhen($this->batch_id && $this->relationLoaded('batch'), function () {
                $batch = $this->batch;
                return [
                    'batch' => [
                        'id' => $batch->id,
                        'name' => $batch->name,
                        'code' => $batch->code,
                        'start_date' => $batch->start_date,
                        'end_date' => $batch->end_date,
                        'capacity' => $batch->capacity,
                        'enrolled_count' => $batch->enrolled_count,
                        'status' => $batch->status,
                    ],
                ];
            }),

        ];
    }
}
