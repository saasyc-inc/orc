CREATE TABLE `ocr_bankcard`
(
    `id`               bigint(20) unsigned                     NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
    `app_id`           varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '应用ID',
    `app_key`          varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '应用KEY',
    `secret_key`       varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '秘钥',
    `file_url`         varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件URL',
    `request`          text COLLATE utf8mb4_unicode_ci COMMENT '请求数据',
    `response`         text COLLATE utf8mb4_unicode_ci COMMENT '返回响应',
    `status`           tinyint(1) unsigned                     NOT NULL DEFAULT '0' COMMENT '状态 1-成功 2-失败',
    `from_ip`          varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '请求 IP',
    `bank_card_number` varchar(64) COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT '' COMMENT '银行卡卡号',
    `valid_date`       varchar(24) COLLATE utf8mb4_unicode_ci  NOT NULL DEFAULT '' COMMENT '有效期',
    `bank_card_type`   tinyint(1) unsigned                     NOT NULL DEFAULT '0' COMMENT '银行卡类型，0:不能识别; 1: 借记卡; 2: 信用卡',
    `bank_name`        varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '银行名，不能识别时为空',
    `created_at`       timestamp                               NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`       timestamp                               NULL     DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `deleted_at`       timestamp                               NULL     DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci COMMENT ='行驶证识别日志';