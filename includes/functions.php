<?php
//translate
function translateStatus($status) {
    $map = [
        'pending' => 'รอดำเนินการ',
        'processing' => 'กำลังจัดส่ง',
        'shipped' => 'จัดส่งแล้ว',
        'completed' => 'เสร็จสิ้น'
    ];
    return $map[$status] ?? $status;
}
?>