<?php


namespace App\Http\Controllers\Campaign;


use App\Http\Controllers\Controller;
use App\Service\Voucher\Exception\ExceededVoucherCodeUsageException;
use App\Service\Voucher\Exception\FlagServiceException;
use App\Service\Voucher\Exception\UsedBeforeException;
use App\Service\Voucher\Exception\VoucherCodeNotExistsException;
use App\Service\Voucher\UseVoucherService;
use App\Service\Voucher\ValueObject\VoucherValueObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UseVoucherController extends Controller
{
    const IRAN_MOBILE_REGEX = '/(0|\+98)?([]|[()]){0,2}9[0-9]([]|[()]){0,2}(?:[0-9]([]|[()]){0,2}){8}/i';
    /**
     * @var UseVoucherService
     */
    protected $useVoucherService;

    /**
     * UseVoucherController constructor.
     * @param UseVoucherService $useVoucherService
     */
    public function __construct(UseVoucherService $useVoucherService)
    {
        $this->useVoucherService = $useVoucherService;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phoneNumber' => ['required', 'regex:' . self::IRAN_MOBILE_REGEX],
            'code'        => 'required|numeric'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $voucher = VoucherValueObject::make()
            ->code($request->input('code'))
            ->phoneNumber($request->input('phoneNumber'));


        try {

            $isWinner = $this->useVoucherService->apply($voucher);

            return response()->json([
                'message'  => trans('message/voucher.you_are_winner'),
                'isWinner' => $isWinner
            ]);

        } catch (VoucherCodeNotExistsException $e) {

            return response()->json([
                'message'  => trans('message/voucher.voucher_code_does_not_exists'),
                'isWinner' => false
            ], 404);

        } catch (ExceededVoucherCodeUsageException $e) {

            return response()->json([
                'message'  => trans('message/voucher.exceeded_limit_voucher_code_usage'),
                'isWinner' => false
            ], 422);
        } catch (FlagServiceException $e) {

            return response()->json([
                'message'  => trans('message/voucher.service_is_disable'),
                'isWinner' => false
            ], 503);
        } catch (UsedBeforeException $e) {

            return response()->json([
                'message'  => trans('message/voucher.you_used_code_before'),
                'isWinner' => false
            ], 503);

        }


    }
}