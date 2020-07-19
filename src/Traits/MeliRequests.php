<?php


namespace Kolovious\MeliSocialite\Traits;


use Kolovious\MeliSocialite\Facade\Meli;

trait MeliRequests
{
    /**
     * @param string $user
     * @return mixed
     */
    public function users($user = 'me')
    {
        return Meli::withAuthToken()->get("users/{$user}");
    }

    /**
     * @param array|null $items
     * @return mixed
     */
    public function items($items = null)
    {
        if ($items) {
            if (is_array($items)) {
                $items = implode(',', $items);
            }
            return Meli::withAuthToken()->get('items', ['ids' => $items]);
        } else {
            $user = $this->currentUser();
            return Meli::withAuthToken()->get("users/{$user->id}/items/search");
        }
    }

    /**
     * @param string|int|null $order
     * @return mixed
     */
    public function getOrders($order = null)
    {
        $user = $this->currentUser();
        $params = ['seller' => $user->id];
        if ($order) {
            $params['q'] = $order;
        }
        return $this->response(Meli::withAuthToken()->get('orders/search', $params))->results;
    }

    /**
     * @param string|int|null $order
     * @return mixed
     */
    public function getShipments($order)
    {
        return $this->response(Meli::withAuthToken()->get("orders/{$order}/shipments"));
    }

    /**
     * @param $shipment
     * @return mixed
     */
    public function getShipmentLabels($shipment)
    {
        return $this->response(Meli::withAuthToken()->get('shipment_labels', ['shipment_ids' => $shipment, 'savePdf' => 'Y']));
    }

    public function getAnswers()
    {
        return $this->response(Meli::withAuthToken()->post('answers'));
    }

    /**
     * @return object|bool
     */
    private function currentUser()
    {
        return $this->response(Meli::withAuthToken()->get("users/me"));
    }

    /**
     * @return object|null
     */
    public function createUserTest()
    {
        return $this->response(Meli::post('users/test_user', ['site_id' => 'MLB']));
    }

    /**
     * @param array $request
     * @return object|array|null
     */
    private function response($request)
    {
        if ($request['httpCode'] === 200) {
            return $request['body'];
        }
        return null;
    }
}
