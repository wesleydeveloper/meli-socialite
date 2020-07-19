<?php


namespace Kolovious\MeliSocialite\Traits;


use Kolovious\MeliSocialite\Facade\Meli;

trait MeliRequests
{
    /**
     * Get user by id or current user
     * @param string|int|null $user
     * @return object|null
     */
    public function getUser($user = 'me')
    {
        return $this->response(Meli::withAuthToken()->get("users/{$user}"));
    }

    /**
     * Get all items or get item by id.
     * @param array|string|null $items
     * @param array|null $options
     * @return object|null
     */
    public function getItems($items = null, $options = null)
    {

        if ($items) {
            if (is_array($items)) {
                $items = implode(',', $items);
            }
            $params = ['ids' => $items];
            if($options){
                $params = array_merge($params, $options);
            }
            return $this->response(Meli::withAuthToken()->get('items', $params));
        } else {
            $user = $this->getUser();
            $params = ['order' => 'last_updated_desc'];
            if($options){
                $params = array_merge($params, $options);
            }
            return $this->response(Meli::withAuthToken()->get("users/{$user->id}/items/search", $params));
        }
    }

    /**
     * Get orders all or get order by id
     * @param string|int|null $order
     * @param array|null $options
     * @return object|null
     */
    public function getOrders($order = null, $options = null)
    {
        $user = $this->getUser();
        $params = ['seller' => $user->id, 'sort' => 'date_desc'];
        if ($order) {
            $params['q'] = $order;
        }
        if($options){
            $params = array_merge($params, $options);
        }
        return $this->response(Meli::withAuthToken()->get('orders/search', $params));
    }

    /**
     * Get shipments by order id
     * @param string|int $order
     * @return object|null
     */
    public function getShipments($order)
    {
        return $this->response(Meli::withAuthToken()->get("orders/{$order}/shipments"));
    }

    /**
     * Get shipment Label pdf by id
     * @param string|int $shipment
     * @return object|null
     */
    public function getShipmentLabels($shipment)
    {
        return $this->response(Meli::withAuthToken()->get('shipment_labels', ['shipment_ids' => $shipment, 'savePdf' => 'Y']));
    }


    /**
     * Send answer
     * @param string|int $question
     * @param string $answer
     * @return object|null
     */
    public function sendAnswer($question, $answer){
        $params = ['question_id' => $question, 'text' => $answer];
        return $this->response(Meli::withAuthToken()->post('answers', $params));
    }

    /**
     * Get all questions
     * @param array|null $options
     * @return object|null
     */
    public function getQuestions($options = null)
    {
        return $this->response(Meli::withAuthToken()->get('my/received_questions/search', $options));
    }

    /**
     * Create user test
     * @return object|null
     */
    public function createUserTest()
    {
        return $this->response(Meli::post('users/test_user', ['site_id' => 'MLB']));
    }

    /**
     * Validate response
     * @param array $request
     * @return object|null
     */
    private function response($request)
    {
        if ($request['httpCode'] === 200) {
            return $request['body'];
        }
        return null;
    }
}
