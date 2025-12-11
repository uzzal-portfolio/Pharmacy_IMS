<?php
class Sales
{
    private $conn;
    private $table_name = "sales";

    public $id;
    public $transaction_id;
    public $medicine_id;
    public $customer_id;
    public $quantity;
    public $total_price;
    public $sale_date;

    public $payment_method;
    public $discount;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function create()
    {
        $query = "INSERT INTO " . $this->table_name . " SET transaction_id=:transaction_id, medicine_id=:medicine_id, customer_id=:customer_id, quantity=:quantity, total_price=:total_price, payment_method=:payment_method, discount=:discount";
        $stmt = $this->conn->prepare($query);

        $this->transaction_id = htmlspecialchars(strip_tags($this->transaction_id));
        $this->medicine_id = htmlspecialchars(strip_tags($this->medicine_id));
        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->discount = htmlspecialchars(strip_tags($this->discount));

        $stmt->bindParam(":transaction_id", $this->transaction_id);
        $stmt->bindParam(":medicine_id", $this->medicine_id);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":quantity", $this->quantity);
        $stmt->bindParam(":total_price", $this->total_price);
        $stmt->bindParam(":payment_method", $this->payment_method);
        $stmt->bindParam(":discount", $this->discount);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    function createTransaction($items, $customer_id, $transaction_id, $total_discount, $payment_method)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Calculate Grand Total for discount distribution
            $grand_total = 0;
            foreach ($items as $item) {
                $grand_total += ($item['price'] * $item['qty']);
            }

            foreach ($items as $item) {
                // 2. Check stock
                $query = "SELECT quantity FROM medicines WHERE id = ? FOR UPDATE";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $item['id']);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$row || $row['quantity'] < $item['qty']) {
                    $this->conn->rollBack();
                    return "Insufficient stock for medicine ID: " . $item['id'];
                }

                // 3. Deduct stock
                $new_qty = $row['quantity'] - $item['qty'];
                $updateQuery = "UPDATE medicines SET quantity = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(1, $new_qty);
                $updateStmt->bindParam(2, $item['id']);
                $updateStmt->execute();

                // 4. Calculate Item Discount Share
                $item_total = $item['price'] * $item['qty'];
                $item_discount = 0;
                if ($grand_total > 0) {
                    $item_discount = ($item_total / $grand_total) * $total_discount;
                }

                // 5. Insert Sale Record
                $this->transaction_id = $transaction_id;
                $this->medicine_id = $item['id'];
                $this->customer_id = $customer_id;
                $this->quantity = $item['qty'];
                $this->total_price = $item_total;
                $this->payment_method = $payment_method;
                $this->discount = number_format($item_discount, 2, '.', ''); // Format to 2 decimal match DB

                if (!$this->create()) {
                    $this->conn->rollBack();
                    return "Failed to record sale";
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return $e->getMessage();
        }
    }

    function read()
    {
        $query = "SELECT s.id, s.transaction_id, m.name as medicine_name, c.name as customer_name, s.quantity, s.total_price, s.discount, s.payment_method, s.sale_date FROM " . $this->table_name . " s LEFT JOIN medicines m ON s.medicine_id = m.id LEFT JOIN customers c ON s.customer_id = c.id ORDER BY s.sale_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function readOne()
    {
        $query = "SELECT s.id, s.transaction_id, m.name as medicine_name, c.name as customer_name, s.quantity, s.total_price, s.sale_date FROM " . $this->table_name . " s LEFT JOIN medicines m ON s.medicine_id = m.id LEFT JOIN customers c ON s.customer_id = c.id WHERE s.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->transaction_id = $row['transaction_id'];
        $this->medicine_id = $row['medicine_name'];
        $this->customer_id = $row['customer_name'];
        $this->quantity = $row['quantity'];
        $this->total_price = $row['total_price'];
        $this->sale_date = $row['sale_date'];
    }

    function readByTransactionId($transaction_id)
    {
        $query = "SELECT s.id, s.transaction_id, m.name as medicine_name, c.name as customer_name, c.phone as customer_phone, s.quantity, s.total_price, s.discount, s.payment_method, s.sale_date, m.price as unit_price FROM " . $this->table_name . " s LEFT JOIN medicines m ON s.medicine_id = m.id LEFT JOIN customers c ON s.customer_id = c.id WHERE s.transaction_id = ? ORDER BY s.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $transaction_id);
        $stmt->execute();
        return $stmt;
    }
}
?>