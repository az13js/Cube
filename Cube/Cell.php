<?php
/**
 * PHP auto gradient descent model.
 *
 * @author az13js <1654602334@qq.com>
 */
namespace App\Cube;

/**
 * y = a2 * x^2 + a1 * x + a0
 * If have more input:
 * y1 = a12 * x1^2 + a11 * x1 + a10
 * y2 = a22 * x2^2 + a21 * x2 + a20
 * y = y1 + y2
 */
class Cell
{
    /** @var array */
    protected $params = array();

    /** @var array 结构与$params对应 */
    protected $gradients = array();

    /** @var array */
    protected $lastApply = array();

    /** @var float */
    protected $y = 0;

    /**
     * 构造方法
     *
     * @param int $inputNumbers 输入这个单元的变量数量
     */
    public function __construct($inputNumbers)
    {
        if ($inputNumbers < 1) {
            throw new \Exception("inputNumbers = $inputNumbers");
        }
        $this->params = array();
        for ($i = 0; $i < $inputNumbers; $i++) {
            $this->params[$i] = array();
            $this->params[$i][0] = 0.0; // a0 -> a0 * x^0
            $this->params[$i][1] = 1.0; // a1 -> a1 * x^1
            $this->params[$i][2] = 0.0; // a2 -> a2 * a^2
        }
        $this->gradients = array();
        for ($i = 0; $i < $inputNumbers; $i++) {
            $this->gradients[$i] = array();
            $this->gradients[$i][0] = 0.0;
            $this->gradients[$i][1] = 0.0;
            $this->gradients[$i][2] = 0.0;
        }
    }

    /**
     * 根据给定的输入计算输出
     *
     * @param array $inputs
     * @return float
     */
    public function apply($inputs)
    {
        if (count($inputs, COUNT_NORMAL) != count($this->params, COUNT_NORMAL)) {
            throw new \Exception("apply() ".count($inputs, COUNT_NORMAL)." != ".count($this->params, COUNT_NORMAL));
        }
        $this->lastApply = $inputs;
        $this->y = 0;
        foreach ($this->gradients as &$da) {
            $da[2] = 0;
            $da[1] = 0;
            $da[0] = 1;
        }
        foreach ($this->params as $i => $a) {
            $square = pow($inputs[$i], 2);
            $this->y += $a[2] * $square;
            $this->y += $a[1] * $inputs[$i];
            $this->y += $a[0];
            $this->gradients[$i][2] += $square;
            $this->gradients[$i][1] += $inputs[$i];
        }
        return $this->y;
    }

    /**
     * 梯度下降
     *
     * 会沿着给定梯度的负方向进行调整
     *
     * @param float $d 对损失函数的梯度
     * @param float $lr 学习率
     * @return array 每个输入变量对损失函数的梯度
     */
    public function gradientDescent($d, $lr = 0.0002)
    {
        $inputs = array();
        foreach ($this->params as $i => &$a) {
            $a2 = $lr * $d * $this->gradients[$i][2];
            $a1 = $lr * $d * $this->gradients[$i][1];
            $a0 = $lr * $d * $this->gradients[$i][0];
            $a[2] -= $a2;
            $a[1] -= $a1;
            $a[0] -= $a0;
            if (!isset($inputs[$i])) {
                $inputs[$i] = 0.5 * $a2 * $this->lastApply[$i] + $a1;
            } else {
                $inputs[$i] += 0.5 * $a2 * $this->lastApply[$i] + $a1;
            }
        }
        return $inputs;
    }
}
