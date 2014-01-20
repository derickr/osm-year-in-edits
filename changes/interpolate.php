<?php

class Interpolate {
    protected $aCoords;
    protected $aCrdX;
    protected $aCrdY;
    protected $aSplines = array();
    protected $iMinX;
    protected $iMaxX;
    protected $iStep;

    protected function prepareCoords(&$aCoords, $iStep, $iMinX = -1, $iMaxX = -1) {
        $this->aCrdX = array();
        $this->aCrdY = array();
        $this->aCoords = array();

        ksort($aCoords);
        foreach ($aCoords as $x => $y) {
            $this->aCrdX[] = $x;
            $this->aCrdY[] = $y;
        }

        $this->iMinX = $iMinX;
        $this->iMaxX = $iMaxX;

        if ($this->iMinX == -1)
            $this->iMinX = min($this->aCrdX);
        if ($this->iMaxX == -1)
            $this->iMaxX = max($this->aCrdX);

        $this->iStep = $iStep;
    }

    public function setInitCoords(&$aCoords, $iStep = 1, $iMinX = -1, $iMaxX = -1) {
        $this->aSplines = array();

        if (count($aCoords) < 4) {
            return false;
        }

        $this->prepareCoords($aCoords, $iStep, $iMinX, $iMaxX);
        $this->buildSpline($this->aCrdX, $this->aCrdY, count($this->aCrdX));
    }

    public function processCoords() {
        for ($x = $this->iMinX; $x <= $this->iMaxX; $x += $this->iStep) {
            $this->aCoords[$x] = $this->funcInterp($x);
        }

        return $this->aCoords;
    }

    private function buildSpline($x, $y, $n) {
        for ($i = 0; $i < $n; ++$i) {
            $this->aSplines[$i]['x'] = $x[$i];
            $this->aSplines[$i]['a'] = $y[$i];
        }
        for ($i = 1; $i < $n; ++$i) {
			$this->aSplines[$i-1]['X'] = $this->aSplines[$i]['x'];
			$this->aSplines[$i-1]['b'] = $this->aSplines[$i]['a'];
        }
		unset($this->aSplines[$n-1]);
    }

    private function funcInterp($x) {
        $n = count($this->aSplines);
		$s = $this->aSplines[0];
        if ($x <= $this->aSplines[0]['x'])  {
            $s = $this->aSplines[0];
        } else {
            if ($x >= $this->aSplines[$n - 1]['x']) {
                $s = $this->aSplines[$n - 1];
            } else {
				for ($i = $n - 1; $i > 0; $i--) {
					if ($x > $this->aSplines[$i]['x']) {
		                $s = $this->aSplines[$i];
						break;
					}
				}
            }
        }
        $dx = ($s['b'] - $s['a']) / ($s['X'] - $s['x']);
		return $s['a'] + (($x - $s['x']) * $dx);
    }
}

?>
