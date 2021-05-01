create table purchase_histories(
order_number INT AUTO_INCREMENT,
user_id INT,
purchase_datetime datetime DEFAULT CURRENT_TIMESTAMP,
primary key(order_number)
);

create table purchase_details(
    detail_number INT AUTO_INCREMENT,
    order_number INT,
    item_id INT,
    amount INT,
    price INT,
    primary key(detail_number)
);
