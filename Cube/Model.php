<?php
/**
 * PHP auto gradient descent model.
 *
 * @author az13js <1654602334@qq.com>
 */
namespace App\Cube;

/* example:
$inputs = [[0, 0], [0, 1], [1, 0], [1, 1]];
$outputs = [[0], [1], [1], [0]];
$model = new Model();
$model->add(new Layer(2, 2));
$model->add(new Layer(2, 1));
for ($j = 0; $j < 1000; $j++) {
    foreach ($inputs as $i => $input) {
        $out = $model->apply($input);
        $error = $model->optimize($outputs[$i], 0.02);
        echo "Loss = ".sprintf("%.6f", $error).PHP_EOL;
    }
}
foreach ($inputs as $i => $input) {
    $out = $model->apply($input);
    echo $out[0].PHP_EOL;
}
echo "Finish".PHP_EOL;
*/

class Model
{
    /** @var array */
    protected $layers = array();

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->layers = array();
    }

    /**
     * 添加层
     *
     * @param Layer $layer 层的实例
     * @return void
     */
    public function add($layer)
    {
        $this->layers[] = $layer;
    }

    /**
     * 根据输入计算输出
     *
     * @param array $inputs
     * @return array
     */
    public function apply($inputs)
    {
        $layerInputs = $inputs;
        foreach ($this->layers as $layer) {
            $layerInputs = $layer->apply($layerInputs); // 算出下一层的输入
        }
        return $layerInputs;
    }

    /**
     * 根据给定的输出进行梯度下降
     *
     * @param array $outputs
     * @param float $lr
     * @return float MSE
     */
    public function optimize($outputs, $lr = 0.0002)
    {
        $layers = count($this->layers, COUNT_NORMAL);
        $lastOut = $this->layers[$layers - 1]->getLastOutputs();
        $losss = array();
        $outputNumber = count($lastOut, COUNT_NORMAL);
        $square = 0;
        for ($i = 0; $i < $outputNumber; $i++) {
            $losss[$i] = ($lastOut[$i] - $outputs[$i]) / (2 * $outputNumber);
            $square += pow($lastOut[$i] - $outputs[$i], 2);
        }
        for ($i = $layers; $i > 0; $i--) {
            $losss = $this->layers[$i - 1]->gradientDescent($losss, $lr);
        }
        return $square / $outputNumber;
    }
}
