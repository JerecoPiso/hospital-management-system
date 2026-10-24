<?php

namespace Database\Seeders;

use App\Models\ExaminationOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExaminationOptionSeeder extends Seeder
{
    /**
     * Default physical examination findings per system. Each option gets a
     * numeric code (its position within its type).
     */
    public function run(): void
    {
        $options = [
            'heent' => [
                'Essentially normal',
                'Abnormal pupillary reaction',
                'Cervical lymphadenopathy',
                'Dry mucous membrane',
                'Icteric sclerae',
                'Pale conjunctivae',
                'Sunken eyeballs',
                'Sunken fontanelle',
            ],
            'chest_lungs' => [
                'Essentially normal',
                'Asymmetrical chest expansion',
                'Decreased breath sounds',
                'Wheezes',
                'Lump/s over breast(s)',
                'Rales/crackles/rhonchi',
                'Intercostal rib/clavicular retraction',
            ],
            'cardiovascular' => [
                'Essentially normal',
                'Displaced apex beat',
                'Heaves and/or thrills',
                'Pericardial bulge',
                'Irregular rhythm',
                'Muffled heart sounds',
                'Murmur',
            ],
            'abdomen' => [
                'Essentially normal',
                'Abdominal rigidity',
                'Abdomen tenderness',
                'Hyperactive bowel sounds',
                'Palpable mass(es)',
                'Tympanitic/dull abdomen',
                'Uterine contraction',
            ],
            'genitourinary' => [
                'Essentially normal',
                'Blood stained in exam finger',
                'Cervical dilatation',
                'Presence of abnormal discharge',
            ],
            'skin' => [
                'Essentially normal',
                'Clubbing',
                'Cold clammy skin',
                'Cyanosis/mottled skin',
                'Edema/swelling',
                'Decreased mobility',
                'Pale nailbeds',
                'Poor skin turgor',
                'Rashes/petechiae',
                'Weak pulses',
            ],
            'neurological' => [
                'Essentially normal',
                'Abnormal gait',
                'Abnormal position sense',
                'Abnormal/decreased sensation',
                'Abnormal reflex(es)',
                'Poor/altered memory',
                'Poor muscle tone/strength',
                'Poor coordination',
            ],
        ];

        foreach ($options as $type => $descriptions) {
            foreach ($descriptions as $index => $description) {
                $option = ExaminationOption::withTrashed()->firstOrNew([
                    'type' => $type,
                    'code' => (string) ($index + 1),
                ]);

                // Model events are muted when run from DatabaseSeeder, so set the pid here
                $option->pid = $option->pid ?? (string) Str::uuid();
                $option->description = $description;
                $option->active = true;
                $option->deleted_at = null;
                $option->save();
            }
        }
    }
}
