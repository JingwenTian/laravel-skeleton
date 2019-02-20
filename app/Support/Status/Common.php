<?php
/**
 * 公共状态码
 *
 * @copyright  eventmosh
 * @author     jingwentian
 * @license    北京活动时文化传媒有限公司
 * @dateTime:  2018/11/19 18:46
 */

namespace App\Support\Status;

/**
 * Class Common.
 *
 * @package App\Support\Status
 */
class Common
{
    /*
    |--------------------------------------------------------------------------
    | 全局状态 [10000 - 10999]
    |--------------------------------------------------------------------------
    | 1. 10000 请求成功(全局唯一成功标识)
    | 2. 10001 请求失败(宽泛的失败标识: 请求失败、数据写入失败、数据获取失败等)
    | 3. 10002 系统参数错误(系统级别的参数错误, 系统必要参数不存在)
    | 4. 10003 请求方法不存在(请求的方法或接口不存在)
    | 5. 10004 系统内部的异常(未捕获的Exception)
    | 6. 10005 系统严重内部错误(语法错误等)
    | 7. 10006 RPC错误(远程过程请求调用失败)
    | 8. 10007 请求超时(服务间调用或第三方服务调用超过最大超时时间)
    | 9. 10008 非法请求(CSRF 验证、签名验证失败等)
    | 10. 10009 系统繁忙(通常用于服务降级)
    | 11. 10010 请求的HTTP METHOD不支持
    | 12. 10011 请求数据为空或不存在
    | 13. 10012 服务间请求签名校验失败(网关->服务,服务<->服务)
    */
    public const CODE_COMMON_SUCCESS = 10000; // 请求成功
    public const CODE_COMMON_FAILED = 10001; // 请求失败
    public const CODE_COMMON_SYSPARAM_ERROR = 10002; //'系统参数错误',
    public const CODE_COMMON_METHOD_NOT_FOUND = 10003; //'请求方法不存在',
    public const CODE_COMMON_INTERNAL_ERROR = 10004; //'系统内部异常',
    public const CODE_COMMON_FATAL_ERROR = 10005; //'系统严重内部错误',
    public const CODE_COMMON_RPC_ERROR = 10006; //'RPC错误',
    public const CODE_COMMON_TIMEOUT = 10007; //'请求超时',
    public const CODE_COMMON_ILLEGAL_REQUEST = 10008; //'非法请求',
    public const CODE_COMMON_SYSTEM_BUSY = 10009; //'系统繁忙', 降级
    public const CODE_COMMON_HTTP_METHOD_UNSUPPORTED = 10010; //'请求的HTTP METHOD不支持',
    public const CODE_COMMON_RESOURCE_NOT_EXIST = 10011; //'资源不存在',
    public const CODE_COMMON_INVALID_CREDENTIALS = 10012; //'服务签名鉴权失败',
    /*
    |--------------------------------------------------------------------------
    | 权限控制 [11000 - 11999]
    |--------------------------------------------------------------------------
    | [110xx]
    | 1. 11000 未登录验证
    | 2. 11001 访问权限被限制(无权限访问)
    | 3. 11002 用户被禁用(黑名单机制)
    | 4. 11003 登录状态异常(触发IP限制机制等)
    |
    | [111xx]
    | 1. 11101 图形验证码错误
    | 2. 11102 手机验证码错误
    | 3. 11103 邮件验证码错误
    */
    public const CODE_COMMON_UNAUTHORIZED = 11000; // '未登录验证'
    public const CODE_COMMON_AUTH_LIMIT = 11001; // '访问权限被限制'
    public const CODE_COMMON_USER_FORBIDDEN = 11002; // '用户被禁用'
    public const CODE_COMMON_USER_BAD_STATUS = 11003; // '登录状态异常'
    public const CODE_COMMON_CAPTCHA_ERROR = 11101; // '图形验证码错误'
    public const CODE_COMMON_PHONE_APTCHA_ERROR = 11102; // '手机验证码错误'
    public const CODE_COMMON_EMAIL_CAPTCHA_ERROR = 11103; // '邮件验证码错误'
    /*
    |--------------------------------------------------------------------------
    | 频次控制 [12000 - 12999]
    |--------------------------------------------------------------------------
    | 1. 12000 请求次数超过限制(请求次数限制、失败次数限制等)
    | 2. 12001 请求时间间隔超过限制(验证码有效期等)
    */
    public const CODE_COMMON_RATE_LIMIT = 12000; // '请求次数超过限制'
    public const CODE_COMMON_TIME_LIMIT = 12001; // '请求时间间隔超过限制'
    /*
    |--------------------------------------------------------------------------
    | 参数或数据验证 [13000 - 13999]
    |--------------------------------------------------------------------------
    | [130xx]
    | 1. 13000 缺少必要的参数(必填项为空)
    | 2. 13001 参数类型错误(string、array、int 等类型判断)
    | 3. 13002 参数值非法(包括非法字符、参数负数非负数判断等)
    | 4. 13003 参数值格式错误(手机号、邮箱、身份证号格式判断)
    | 5. 13004 参数长度错误(用户名>6等场景)
    | 6. 13005 数据解析错误(解析JSON内容错误、Excel解析失败等)
    |
    | [131xx]
    | 1. 13100 不合法的文件类型(图片类型等)
    | 2. 13101 不合法的文件大小(图片大小等)
    | 3. 13102 不存在的媒体数据(通过图片ID获取不到图片等情况)
    */
    public const CODE_COMMON_PARAMETER_MISSING = 13000; //'缺少必要的参数',
    public const CODE_COMMON_PARAMETER_TYPE_INVALID = 13001; //'参数类型错误',
    public const CODE_COMMON_PARAMETER_VALUE_INVALID = 13002; //'参数值非法',
    public const CODE_COMMON_PARAMETER_FORMAT_INVALID = 13003; //'参数值格式错误',
    public const CODE_COMMON_PARAMETER_LENGTH_INVALID = 13004; //'参数长度错误',
    public const CODE_COMMON_PARAMETER_PARSE_FAILED = 13005; //'参数解析错误',
    public const CODE_COMMON_FILE_TYPE_INVALID = 13100; //'不合法的文件类型',
    public const CODE_COMMON_FILE_SIZE_INVALID = 13101; //'不合法的文件大小',
    public const CODE_COMMON_FILE_NOT_FOUND = 13102; //'不存在的媒体数据',
}
