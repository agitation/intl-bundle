<?php
declare(strict_types=1);
/*
 * @package    agitation/intl-bundle
 * @link       http://github.com/agitation/intl-bundle
 * @author     Alexander Günsche
 * @license    http://opensource.org/licenses/MIT
 */

namespace Agit\IntlBundle\Setting;

use Agit\IntlBundle\Tool\Translate;

class InternalLocaleSetting extends AbstractLocaleSetting
{
    public function getId()
    {
        return 'agit.internal_locale';
    }

    public function getName()
    {
        return Translate::t('Internal language');
    }

    public function getDefaultValue()
    {
        return 'en_US';
    }

    public function validate($value)
    {
        $this->validationService->validate(
            'selection',
            $value,
            $this->localeService->getAvailableLocales()
        );
    }
}
