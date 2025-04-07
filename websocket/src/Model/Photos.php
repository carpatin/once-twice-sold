<?php

declare(strict_types=1);

namespace OnceTwiceSold\Model;

class Photos
{
    public function __construct(
        private string $auctionId,
        private string $photo1 = '',
        private string $photo2 = '',
        private string $photo3 = '',
    ) {
        //
    }

    public static function createFromTableRow(string $auctionId, array $photosRow): self
    {
        return new Photos(
            $auctionId,
            $photosRow['photo1'],
            $photosRow['photo2'],
            $photosRow['photo3'],
        );
    }

    public function toTableRow(): array
    {
        return [
            'photo1' => $this->photo1,
            'photo2' => $this->photo2,
            'photo3' => $this->photo3,
        ];
    }

    public function getAuctionId(): string
    {
        return $this->auctionId;
    }

    public function addPhoto(string $photo): bool
    {
        if (empty($this->photo1)) {
            $this->photo1 = $photo;

            return true;
        }

        if (empty($this->photo2)) {
            $this->photo2 = $photo;

            return true;

        }

        if (empty($this->photo3)) {
            $this->photo3 = $photo;

            return true;
        }

        return false;
    }

    public function getPhotos(): array
    {
        $photos = [];
        if (!empty($this->photo1)) {
            $photos[] = $this->photo1;
        }
        if (!empty($this->photo2)) {
            $photos[] = $this->photo2;
        }

        if (!empty($this->photo3)) {
            $photos[] = $this->photo3;
        }

        return $photos;
    }
}
