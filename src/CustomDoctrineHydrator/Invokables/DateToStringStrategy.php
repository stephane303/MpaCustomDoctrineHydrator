<?php
/*
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace CustomDoctrineHydrator\Invokables;

use DateTime;
use IntlDateFormatter;
use Locale;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

class DateToStringStrategy implements StrategyInterface, ServiceLocatorAwareInterface
{
    protected $sm;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function extract($value)
    {
        /** @var $value DateTime */
        if (!is_null($value)) {
            if (!($value instanceof DateTime)) {
                throw new \InvalidArgumentException(sprintf(
                        'Field "%s" is not a valid DateTime object',
                        $value)
                );
            }

            $cdfConfig  = $this->getServiceLocator()->get('Config');
            $dateConfig = $cdfConfig['customdoctrinehydrator']['date'];

            $fmt = new IntlDateFormatter(
                Locale::getDefault(),
                $dateConfig['date_format'],
                $dateConfig['time_format'],
                $value->getTimezone()->getName(),
                $dateConfig['cal_format']
            );

            if ($dateConfig['four_digits_year']) {
                if (substr_count(strtolower($fmt->getPattern()), "y") === 2) {
                    $fmt->setPattern(str_ireplace('y', 'yy', $fmt->getPattern()));
                }
            }

            if ($dateConfig['two_digits_month']) {
                if (substr_count($fmt->getPattern(), "M") === 1) {
                    $fmt->setPattern(str_ireplace('M', 'MM', $fmt->getPattern()));
                }
            }

            if ($dateConfig['two_digits_day']) {
                if (substr_count($fmt->getPattern(), "d") === 1) {
                    $fmt->setPattern(str_ireplace('d', 'dd', $fmt->getPattern()));
                }
            }

            return $fmt->format($value);
        } else {
            return $value;
        }
    }

    public function hydrate($value)
    {
        return $value;
    }
}