<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

use App\Models\StandardStatusModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function PHPSTORM_META\map;

class StandardModel extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $table ='standards';
    protected $fillable = [
        'name',
        'description',
        'factor',
        'dimension',
        'related_standards',
        'narrative',
        'nro_standard',
		'date_id',
        'standard_status_id',
        'registration_status_id'
    ];

    public function users(): BelongsToMany    {
        return $this->belongsToMany(User::class, 'users_standards', 'standard_id', 'user_id')
        ->using(UserStandardModel::class);
    }
    public function standard_status() {
        return $this->belongsTo(StandardStatusModel::class, 'standard_status_id');    }
    /*
    public static function user($standard_id){
        return StandardModel::find($standard_id)->users();
    }*/
    
    public function plans(){
        return $this->hasMany(PlanModel::class,'standard_id');
    }
    public static function exists($standard_id){
        return self::where('id', $standard_id)->exists();
    }
    public static function isActive($standard_id){
        return self::where('id', $standard_id)
                    ->where('registration_status_id', RegistrationStatusModel::registrationActiveId())
                    ->exists();
    }
    public static function existsAndActive($standard_id){
        return self::exists($standard_id) and self::isActive($standard_id);
    }
    public static function create34Standards($year, $semester){
        //Standard #1
        DB::table('standards')->insert([
            'name' => "Propósitos Articulados",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés) Estándar 5 (Pertinencia del perfil de egreso)",
            'nro_standard' => 1,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #2
        DB::table('standards')->insert([
            'name' => "Participación de grupos de interés",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 1 (Propósitos del programa) Estándar 3 (Revisión periódica y participativa de las políticas y objetivos) Estándar 5 (Pertinencia del perfil de egreso) Estándar 6 (Revisión del perfil de egreso) Estándar 8 (Planes de mejora) Estándar 22 (Gestión y calidad de la I+D+i realizada por docentes) Estándar 25 (Responsabilidad social) Estándar 34 (Seguimiento a egresados y objetivos educacionales)",
            'nro_standard' => 2,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #3
        DB::table('standards')->insert([
            'name' => "Revisión periódica y participativa de las políticas y objetivos",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés)",
            'nro_standard' => 3,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #4
        DB::table('standards')->insert([
            'name' => "Sostenibilidad",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 1: PLANIFICACIÓN DEL PROGRAMA DE ESTUDIOS",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 3 (Revisión periódica y participativa de las políticas y objetivos) Estándar 28 (Equipamiento y uso de la infraestructura) Estándar 31 (Centros de información y referencia) Estándar 32 (Recursos Humanos para la gestión del programa de estudios)",
            'nro_standard' => 4,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #5
        DB::table('standards')->insert([
            'name' => "Pertinencia del perfil de egreso",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 2: GESTIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 1 (Propósitos articulados) Estándar 2 (Participación de los grupos de interés) Estándar 9 (Plan de estudios) Estándar 10 (Características del plan de estudios) Estándar 11 (Enfoque por competencias)",
            'nro_standard' => 5,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #6
        DB::table('standards')->insert([
            'name' => "Revisión del perfil de egreso",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 2: GESTIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés) Estándar 5 (Pertinencia del perfil de egreso) Estándar 7 (Sistema de gestión de calidad-SGC) Estándar 9 (Plan de estudios) Estándar 11 (Enfoque por competencias) Estándar 33 (Logro de competencias)",
            'nro_standard' => 6,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #7
        DB::table('standards')->insert([
            'name' => "Sistema de Gestión de la Calidad",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 3: ASEGURAMIENTO DE LA CALIDAD",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 8 (Planes de mejora) Estándar 30 (Sistema de información y comunicación)",
            'nro_standard' => 7,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #8
        DB::table('standards')->insert([
            'name' => "Planes de mejoras",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 3: ASEGURAMIENTO DE LA CALIDAD",
            'dimension' => "DIMENSIÓN 1: GESTIÓN ESTRATÉGICA",
            'related_standards' => "Estándar 7 (Sistema de gestión de la calidad)",
            'nro_standard' => 8,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #9
        DB::table('standards')->insert([
            'name' => "Plan de estudios",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 4: PROCESO ENSEÑANZA-APRENDIZAJE",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 5 (Pertinencia del perfil de egreso) Estándar 6 (Revisión del perfil de egreso) Estándar 11 (Enfoque por competencias) Estándar 34 (Seguimiento a egresados y objetivos educacionales)",
            'nro_standard' => 9,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #10
        DB::table('standards')->insert([
            'name' => "Características del plan de estudios",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 4: PROCESO ENSEÑANZA-APRENDIZAJE",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 5 (Pertinencia del perfil de egreso) Estándar 11 (Enfoque por Competencias) Estándar 12 (Articulación con I+D+i y responsabilidad social) Estándar 20 (Seguimiento al desempeño de los estudiantes) Estándar 34 (Seguimiento a egresados y objetivos educacionales)",
            'nro_standard' => 10,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #11
        DB::table('standards')->insert([
            'name' => "Enfoque por competencias",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 4: PROCESO ENSEÑANZA-APRENDIZAJE",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 7 (Sistema de gestión de calidad) Estándar 9 (Plan de estudios) Estándar 14 (Selección, evaluación, capacitación y perfeccionamiento) Estándar 20 (Seguimiento al desempeño de los estudiantes) Estándar 33 (Logro de competencias)",
            'nro_standard' => 11,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #12
        DB::table('standards')->insert([
            'name' => "Articulación con I+D+i y responsabilidad social",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 4: PROCESO ENSEÑANZA-APRENDIZAJE",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 10 (Características del plan de estudios) Estándar 11 (Enfoque por competencias) Estándar 11 (Enfoque por competencias) Estándar 16 (Reconocimiento de las actividades de labor docente) Estándar 33 (Logro de competencias)",
            'nro_standard' => 12,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #13
        DB::table('standards')->insert([
            'name' => "Movilidad",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 4: PROCESO ENSEÑANZA-APRENDIZAJE",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 15 (Plana docente adecuada) Estándar 33 (Logro de competencias)",
            'nro_standard' => 13,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #14
        DB::table('standards')->insert([
            'name' => "Selección, evaluación, capacitación y perfeccionamiento",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 5: GESTIÓN DE LOS DOCENTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 10 (Características del plan de estudios) Estándar 15 (Plana docente adecuada)",
            'nro_standard' => 14,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #15
        DB::table('standards')->insert([
            'name' => "Plana docente adecuada",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 5: GESTIÓN DE LOS DOCENTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 1 (Propósitos articulados) Estándar 10 (Características del plan de estudios) Estándar 14 (Selección, evaluación, capacitación y perfeccionamiento)",
            'nro_standard' => 15,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #16
        DB::table('standards')->insert([
            'name' => "Reconocimiento de las actividades de labor docente",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 5: GESTIÓN DE LOS DOCENTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 14 (Selección, evaluación, capacitación y perfeccionamiento) Estándar 15 (Plana docente adecuada)",
            'nro_standard' => 16,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #17
        DB::table('standards')->insert([
            'name' => "Plan de desarrollo académico del docente",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 5: GESTIÓN DE LOS DOCENTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 4 (Sostenibilidad) Estándar 14 (Selección, evaluación, capacitación y perfeccionamiento) Estándar 15 (Plana docente adecuada) Estándar 22 (Gestión y calidad de la I+D+i realizada por docentes) Estándar 23 (I+D+i para la obtención del grado y título) Estándar 33 (Logro de competencias)",
            'nro_standard' => 17,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #18
        DB::table('standards')->insert([
            'name' => "Admisión al programa de estudios",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 6: SEGUIMIENTO A ESTUDIANTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 6 (Revisión del perfil de egreso) Estándar 19 (Nivelación de ingresantes) Estándar 33 (Logro de competencias)",
            'nro_standard' => 18,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #19
        DB::table('standards')->insert([
            'name' => "Nivelación de ingresantes",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 6: SEGUIMIENTO A ESTUDIANTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 18 (Admisión al programa de estudios) Estándar 20 (Seguimiento al desempeño de los estudiantes) Estándar 21 (Actividades extracurriculares) Estándar 33 (Logro de competencias)",
            'nro_standard' => 19,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #20
        DB::table('standards')->insert([
            'name' => "Seguimiento al desempeño de los estudiantes",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 6: SEGUIMIENTO A ESTUDIANTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 18 (Admisión al programa de estudios) Estándar 19 (Nivelación de ingresantes) Estándar 21 (Actividades extracurriculares) Estándar 23 (I+D+i para la obtención del grado y el título) Estándar 27 (Bienestar) Estándar 33 (Logro de competencias)",
            'nro_standard' => 20,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #21
        DB::table('standards')->insert([
            'name' => "Actividades extracurriculares",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 6: SEGUIMIENTO A ESTUDIANTES",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 5 (Pertinencia del perfil de egreso) Estándar 20 (Seguimiento al desempeño de los estudiantes) Estándar 33 (Logro de competencias) Estándar 27 (Bienestar) Estándar 25 (Responsabilidad social)",
            'nro_standard' => 21,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #22
        DB::table('standards')->insert([
            'name' => "Gestión y calidad de la I+D+i realizada por docentes",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 7: INVESTIGACIÓN, DESARROLLO TECNOLÓGICO E INNOVACIÓN",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 1 (Propósitos articulados) Estándar 3 (Revisión periódica y participativa de las políticas y objetivos) Estándar 4 (Sostenibilidad) Estándar 12 (Articulación con I+D+i y responsabilidad social) Estándar 15 (Plana docente adecuada)",
            'nro_standard' => 22,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #23
        DB::table('standards')->insert([
            'name' => "I+D+i para la obtención del grado y el título",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 7: INVESTIGACIÓN, DESARROLLO TECNOLÓGICO E INNOVACIÓN",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 1 (Propósitos articulados) Estándar 3 (Revisión periódica y participativa de las políticas y objetivos) Estándar 10 (Características del plan de estudios) Estándar 12 (Articulación con I+D+i y responsabilidad social) Estándar 31 (Centros de información y referencia)",
            'nro_standard' => 23,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #24
        DB::table('standards')->insert([
            'name' => "Publicaciones de los resultados de I+D+i",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 7: INVESTIGACIÓN, DESARROLLO TECNOLÓGICO E INNOVACIÓN",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 12 (Articulación con I+D+i y responsabilidad social) Estándar 22 (Gestión y calidad de la I+D+i realizada por docentes) Estándar 23 (I+D+i para la obtención del grado y el título) Estándar 31 (Centros de información y referencia)",
            'nro_standard' => 24,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #25
        DB::table('standards')->insert([
            'name' => "Responsabilidad social",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 8: RESPONSABILIDAD SOCIAL UNIVERSITARIA",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 2 (Participación de los grupos de interés) Estándar 4 (Sostenibilidad) Estándar 11(Enfoque por competencias) Estándar 12 (Articulación con I+D+i y responsabilidad social) Estándar 22 (Gestión y calidad de la I+D+i realizada por docentes) Estándar 26 (Implementación de políticas ambientales)",
            'nro_standard' => 25,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #26
        DB::table('standards')->insert([
            'name' => "Implementación de políticas ambientales",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 8: RESPONSABILIDAD SOCIAL UNIVERSITARIA",
            'dimension' => "DIMENSIÓN 2: FORMACIÓN INTEGRAL",
            'related_standards' => "Estándar 3 (Revisión periódica y participativa de las políticas y objetivos) Estándar 25 (Responsabilidad social)",
            'nro_standard' => 26,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);

        //Standard #27
        DB::table('standards')->insert([
            'name' => "Bienestar",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 9: SERVICIOS DE BIENESTAR",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 4 (Sostenibilidad)",
            'nro_standard' => 27,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #28
        DB::table('standards')->insert([
            'name' => "Equipamiento y uso de la infraestructura",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 10: INFRAESTRUCTURA Y SOPORTE",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 4 (Sostenibilidad) Estándar 29 (Mantenimiento de la infraestructura)",
            'nro_standard' => 28,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #29
        DB::table('standards')->insert([
            'name' => "Mantenimiento de la infraestructura",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 10: INFRAESTRUCTURA Y SOPORTE",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 4 (Sostenibilidad)",
            'nro_standard' => 29,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #30
        DB::table('standards')->insert([
            'name' => "Sistema de información y comunicación",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 10: INFRAESTRUCTURA Y SOPORTE",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 7 (Sistema de gestión de la calidad) Estándar 18 (Admisión al programa de estudios) Estándar 19 (Nivelación de ingresantes) Estándar 20 (Seguimiento al desempeño de los estudiantes)",
            'nro_standard' => 30,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #31
        DB::table('standards')->insert([
            'name' => "Centros de información y referencia",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 10: INFRAESTRUCTURA Y SOPORTE",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 9 (Plan de estudios)",
            'nro_standard' => 31,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #32
        DB::table('standards')->insert([
            'name' => "Recursos humanos para la gestión del programa de estudios",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 11: RECURSOS HUMANOS",
            'dimension' => "DIMENSIÓN 3: SOPORTE INSTITUCIONAL",
            'related_standards' => "Estándar 1 (Propósitos articulados) Estándar 14 (Selección, evaluación, capacitación y perfeccionamiento) Estándar 15 (Plana docente adecuada) Estándar 17 (Plan de desarrollo académico del docente)",
            'nro_standard' => 32,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #33
        DB::table('standards')->insert([
            'name' => "Logro de competencias",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 12: VERIFICACIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 4: RESULTADOS",
            'related_standards' => "Estándar 5 (Pertinencia del perfil de egreso) Estándar 6 (Revisión del perfil de egreso) Estándar 10 (Características del plan de estudios) Estándar 11 (Enfoque por competencias)",
            'nro_standard' => 33,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
        //Standard #34
        DB::table('standards')->insert([
            'name' => "Seguimiento a egresados y objetivos educacionales",
            'description' => "Esta es a descripción del estándar",
            'factor' => "FACTOR 12: VERIFICACIÓN DEL PERFIL DE EGRESO",
            'dimension' => "DIMENSIÓN 4: RESULTADOS",
            'related_standards' => "Estándar 10 (Características del plan de estudios) Estándar 33 (Logro de competencias)",
            'nro_standard' => 34,
            'date_id' => DateModel::dateId($year, $semester),
            'standard_status_id' => StandardStatusModel::standardStatusId('no logrado'),
            'registration_status_id' =>   RegistrationStatusModel::registrationActiveId()
        ]);
    }
}
