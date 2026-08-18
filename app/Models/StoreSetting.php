<?php

namespace App\Models;

use App\Core\Database;

class StoreSetting
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    public function findByStoreId(int $storeId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM store_settings WHERE store_id = ?", [$storeId]);
    }

    public function findByMerchantId(int $merchantId): ?array
    {
        return $this->db->fetchOne("SELECT * FROM store_settings WHERE merchant_id = ?", [$merchantId]);
    }

    public function createOrUpdate(int $storeId, int $merchantId, array $data): bool
    {
        $existing = $this->findByStoreId($storeId);

        $allowed = [
            'logo', 'favicon', 'primary_color', 'secondary_color',
            'hero_title', 'hero_subtitle', 'hero_image',
            'whatsapp_number', 'contact_email', 'contact_phone',
            'business_address', 'facebook_url', 'instagram_url', 'twitter_url',
            'footer_text', 'theme_name', 'razorpay_key_id', 'razorpay_key_secret', 'razorpay_connected'
        ];

        $cleanData = array_intersect_key($data, array_flip($allowed));

        if ($existing) {
            return $this->db->update('store_settings', $cleanData, 'store_id = ?', [$storeId]) >= 0;
        } else {
            $cleanData['store_id'] = $storeId;
            $cleanData['merchant_id'] = $merchantId;
            return $this->db->insert('store_settings', $cleanData) > 0;
        }
    }
}
