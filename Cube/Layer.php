<?php
/**
 * PHP auto gradient descent model.
 *
 * @author az13js <1654602334@qq.com>
 */
namespace App\Cube;

class Layer
{
    /** @var array */
    protected $cells = array();

    /** @var array */
    protected $lastApply = array();

    /** @var array 每个输入对损失函数的梯度 */
    protected $gradients = array();

    /** @var float */
    protected $y = array();

    /**
     * 构造方法
     */
    public function __construct($inputNumbers, $units)
    {
        $this->cells = array();
        for ($i = 0; $i < $units; $i++) {
            $this->cells[$i] = new Cell($inputNumbers);
        }
        $this->gradients = array();
        $this->lastApply = array();
        for ($i = 0; $i < $inputNumbers; $i++) {
            $this->gradients[$i] = 0;
            $this->lastApply[$i] = 0;
        }
    }

    /**
     * 根据给定的输入计算输出
     *
     * @param array $inputs
     * @return array
     */
    public function apply($inputs)
    {
        if (count($inputs, COUNT_NORMAL) != count($this->lastApply, COUNT_NORMAL)) {
            throw new \Exception("apply() ".count($inputs, COUNT_NORMAL)." != ".count($this->lastApply, COUNT_NORMAL));
        }
        $this->lastApply = $inputs;
        $this->y = array();
        foreach ($this->cells as $i => $cell) {
            $this->y[$i] = $cell->apply($inputs);
        }
        return $this->y;
    }

    /**
     * 梯度下降
     *
     * 会沿着给定梯度的负方向进行调整
     *
     * @param array $d 每个输出变量对损失函数的梯度
     * @param float $lr 学习率
     * @return array 每个输入变量对损失函数的梯度
     */
    public function gradientDescent($d, $lr = 0.0002)
    {
        $this->gradients = array();
        foreach ($this->lastApply as $v) {
            $this->gradients[] = 0;
        }
        foreach ($this->cells as $i => $cell) {
            $inputs = $cell->gradientDescent($d[$i], $lr);
            foreach ($inputs as $j => $gradient) {
                $this->gradients[$j] += $gradient;
            }
        }
        return $this->gradients;
    }

    /**
     * @return array
     */
    public function getLastOutputs()
    {
        return $this->y;
    }
}
