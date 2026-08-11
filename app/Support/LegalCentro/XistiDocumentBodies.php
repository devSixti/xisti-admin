<?php

namespace App\Support\LegalCentro;

class XistiDocumentBodies
{
    /**
     * @return array{title: string, summary: string, body: string}|null
     */
    public static function resolve(string $slug, string $lang): ?array
    {
        $lang = strtolower(substr($lang, 0, 2));

        return match ($slug) {
            'terminos' => self::terminos($lang),
            'privacidad' => self::privacidad($lang),
            'tratamiento-datos' => self::tratamientoDatos($lang),
            'condiciones-usuario' => self::condicionesUsuario($lang),
            'condiciones-conductor' => self::condicionesConductor($lang),
            'aviso-legal' => self::avisoLegal($lang),
            'seguridad' => self::seguridad($lang),
            'faq' => self::faq($lang),
            'contacto' => self::contacto($lang),
            'pqr' => self::pqr($lang),
            'cookies' => self::cookies($lang),
            'eliminar-cuenta' => self::eliminarCuenta($lang),
            default => null,
        };
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function terminos(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Terms and Conditions of Use — :brand_name',
                'summary' => 'Terms governing use of the :brand_name mobility platform operated by :entity_name. :brand_name is a technology connector, not a transport provider.',
                'body' => self::terminosEnBody(),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Términos y Condiciones de Uso — :brand_name',
                'summary' => 'Condiciones que regulan el acceso y uso de la plataforma :brand_name, operada por :entity_name. Versión :consent_version, actualizada el :last_updated.',
                'body' => self::terminosEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function privacidad(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Privacy Policy — :brand_name',
                'summary' => 'How :entity_name collects, uses, and protects personal data under Colombian Habeas Data law (Law 1581 of 2012).',
                'body' => self::privacidadEnBody(),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Política de Privacidad y Habeas Data — :brand_name',
                'summary' => 'Tratamiento de datos personales conforme a la Ley 1581 de 2012 y normativa de Habeas Data en Colombia. Responsable: :entity_name.',
                'body' => self::privacidadEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function tratamientoDatos(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Personal Data Processing Policy — :brand_name',
                'summary' => ':entity_name processes personal data under Colombian Law 1581 of 2012. This policy describes purposes, legal bases, retention, and data-subject rights.',
                'body' => self::enSummaryBody(
                    'Personal Data Processing Policy',
                    'tratamiento-datos',
                    '<p>:entity_name, identified with NIT :entity_nit and address at :entity_address, :entity_country, is the data controller for personal information processed through :brand_name.</p>'
                    . '<p>We collect identification, contact, location, payment, and usage data to operate the platform, connect users with independent drivers, process payments, ensure security, and comply with legal obligations. Processing is based on consent, contractual necessity, legitimate interest, and applicable law.</p>'
                    . '<p>Data subjects may exercise access, rectification, deletion, and objection rights by writing to :privacy_email. For the complete policy in Spanish, including detailed categories, retention periods, and transfer rules, see the authoritative version linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Política de Tratamiento de Datos Personales — :brand_name',
                'summary' => 'Política de tratamiento alineada a la Ley 1581 de 2012. Responsable del tratamiento: :entity_name (NIT :entity_nit).',
                'body' => self::tratamientoDatosEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function condicionesUsuario(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Passenger Terms — :brand_name',
                'summary' => 'Conditions for passengers using :brand_name to request trips, deliveries, haulage, and shared rides with independent drivers.',
                'body' => self::enSummaryBody(
                    'Passenger Terms and Conditions',
                    'condiciones-usuario',
                    '<p>By using :brand_name as a passenger, you agree to provide accurate information, use the platform lawfully, and enter into direct agreements with independent drivers for the requested service.</p>'
                    . '<p>:brand_name facilitates connection, fare negotiation, payment processing, and trip coordination. You are responsible for verifying driver credentials, following safety guidelines, and complying with local regulations. Cancellations and refunds follow the rates and policies displayed in the app at the time of booking.</p>'
                    . '<p>For full passenger obligations, prohibited conduct, and dispute procedures in Spanish, refer to the complete document linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Condiciones para Usuarios (Pasajeros) — :brand_name',
                'summary' => 'Reglas aplicables a pasajeros que solicitan viajes, encomiendas, acarreos y recorridos compartidos a través de :brand_name.',
                'body' => self::condicionesUsuarioEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function condicionesConductor(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Independent Driver Terms — :brand_name',
                'summary' => 'Requirements and obligations for independent drivers offering availability on :brand_name, including documentation, commissions, and wallet settlements.',
                'body' => self::enSummaryBody(
                    'Independent Driver Terms',
                    'condiciones-conductor',
                    '<p>Drivers on :brand_name operate as independent contractors, not employees of :entity_name. You must maintain valid licenses, vehicle documentation, insurance where required, and pass identity verification.</p>'
                    . '<p>Commissions, wallet balances, payouts, and service fees are disclosed in the driver app. You set or accept fares within platform rules and are solely responsible for tax obligations, traffic compliance, and passenger safety during services you provide.</p>'
                    . '<p>The complete driver agreement in Spanish covers document renewal, deactivation grounds, and wallet policies. See the link below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Condiciones para Conductores Independientes — :brand_name',
                'summary' => 'Requisitos documentales, comisiones, billetera digital y obligaciones de conductores independientes en :brand_name.',
                'body' => self::condicionesConductorEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function avisoLegal(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Legal Notice — :brand_name',
                'summary' => 'Corporate identification, intellectual property, and liability limitations for :entity_name and the :brand_name platform.',
                'body' => self::avisoLegalEnBody(),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Aviso Legal — :brand_name',
                'summary' => 'Información corporativa de :entity_name, propiedad del sitio y limitaciones de responsabilidad de la plataforma :brand_name.',
                'body' => self::avisoLegalEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function seguridad(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Safety — :brand_name',
                'summary' => 'SOS features, identity verification, and recommended safety practices for users and independent drivers on :brand_name.',
                'body' => self::enSummaryBody(
                    'Safety on :brand_name',
                    'seguridad',
                    '<p>:brand_name includes in-app SOS tools, trip sharing, and identity verification to support safer interactions between users and independent drivers. In an emergency, contact local authorities first, then use SOS to alert trusted contacts and platform support.</p>'
                    . '<p>We recommend verifying vehicle and driver details before boarding, sharing trip status with a contact, and reporting suspicious behavior through the app or at :support_email.</p>'
                    . '<p>Detailed safety procedures, verification steps, and community guidelines are available in the full Spanish document linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Seguridad — :brand_name',
                'summary' => 'Botón SOS, verificación de identidad y buenas prácticas de seguridad para usuarios y conductores independientes.',
                'body' => self::seguridadEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function faq(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Frequently Asked Questions — :brand_name',
                'summary' => 'Common questions about :brand_name as a connection platform, fares, independent drivers, location data, and support channels.',
                'body' => self::enSummaryBody(
                    'Frequently Asked Questions',
                    'faq',
                    '<p><strong>What is :brand_name?</strong> A technology platform that connects users with independent drivers for trips, deliveries, haulage, and shared rides. :brand_name does not operate vehicles or provide transport services.</p>'
                    . '<p><strong>How are fares set?</strong> Users propose a value or contribution; independent drivers may accept or negotiate within app rules. Payments are processed through integrated methods shown at checkout.</p>'
                    . '<p><strong>How do I get help?</strong> Use in-app chat or email :support_email. For privacy requests contact :privacy_email; for formal complaints see our PQR channel at :pqr_email.</p>'
                    . '<p>Twelve or more detailed questions and answers are available in the full Spanish FAQ linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Preguntas Frecuentes — :brand_name',
                'summary' => 'Respuestas sobre la naturaleza de :brand_name, tarifas, conductores independientes, ubicación, pagos y soporte.',
                'body' => self::faqEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function contacto(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Contact — :brand_name',
                'summary' => 'Official contact channels for :entity_name and the :brand_name platform.',
                'body' => self::enSummaryBody(
                    'Contact Us',
                    'contacto',
                    '<p><strong>General support:</strong> <a href="mailto::support_email">:support_email</a></p>'
                    . '<p><strong>Privacy and data rights:</strong> <a href="mailto::privacy_email">:privacy_email</a></p>'
                    . '<p><strong>Legal inquiries:</strong> <a href="mailto::legal_email">:legal_email</a></p>'
                    . '<p><strong>Entity:</strong> :entity_name — NIT :entity_nit, :entity_address, :entity_country. Website: <a href=":public_url">:public_url</a> · Legal center index: <a href=":centro_legal_url">:centro_legal_url</a></p>'
                    . '<p>In-app live chat is available from the user menu. For complete contact details and response times in Spanish, see the full page linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Contacto — :brand_name',
                'summary' => 'Canales oficiales de :entity_name para soporte, privacidad, asuntos legales y PQR.',
                'body' => self::contactoEsBody(),
            ];
        }

        return null;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function pqr(string $lang): ?array
    {
        if ($lang === 'en') {
            return [
                'title' => 'Petitions, Complaints, and Claims (PQR) — :brand_name',
                'summary' => 'How to submit PQR requests to :entity_name under Colombian consumer law (Law 1480 of 2011), including response timeframes.',
                'body' => self::enSummaryBody(
                    'Petitions, Complaints, and Claims (PQR)',
                    'pqr',
                    '<p>Users may submit petitions (<em>peticiones</em>), complaints (<em>quejas</em>), or claims (<em>reclamos</em>) regarding :brand_name services by email to <a href="mailto::pqr_email">:pqr_email</a> or through the in-app support channel.</p>'
                    . '<p>Include your full name, identification, contact details, and a clear description of the facts. Under Law 1480 of 2011, :entity_name will acknowledge receipt and respond within the legally established timeframes.</p>'
                    . '<p>Full PQR procedures, required information, and escalation paths are described in the complete Spanish document linked below.</p>'
                ),
            ];
        }

        if ($lang === 'es') {
            return [
                'title' => 'Peticiones, Quejas y Reclamos (PQR) — :brand_name',
                'summary' => 'Procedimiento PQR de :entity_name conforme a la Ley 1480 de 2011, plazos de respuesta y canales oficiales.',
                'body' => self::pqrEsBody(),
            ];
        }

        return null;
    }

    private static function enSummaryBody(string $heading, string $slug, string $paragraphs): string
    {
        return '<div class="legal-prose">'
            . '<p class="legal-meta"><strong>Version:</strong> :consent_version · <strong>Last updated:</strong> :last_updated</p>'
            . '<h2>' . $heading . '</h2>'
            . $paragraphs
            . '<p class="legal-lang-note">The authoritative full text of this document is available in Spanish: '
            . '<a href=":public_url/legal/' . $slug . '?lang=es">Ver versión en español</a>.</p>'
            . '</div>';
    }

    private static function terminosEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated · <strong>Operador:</strong> :entity_name (NIT :entity_nit)</p>

<h2 id="objeto">1. Objeto</h2>
<p>Los presentes Términos y Condiciones regulan el acceso y uso de la plataforma digital <strong>:brand_name</strong>, disponible en <a href=":public_url">:public_url</a> y aplicaciones móviles asociadas, operada por <strong>:entity_name</strong>, domiciliada en :entity_address, :entity_country.</p>
<p>Al registrarse, acceder o utilizar :brand_name, el Usuario declara haber leído, comprendido y aceptado estos Términos, la <a href=":public_url/legal/privacidad?lang=es">Política de Privacidad</a> y la <a href=":public_url/legal/tratamiento-datos?lang=es">Política de Tratamiento de Datos Personales</a>.</p>

<h2 id="naturaleza-plataforma">2. Naturaleza de la plataforma — :brand_name NO es transportista</h2>
<p><strong>:brand_name es una plataforma tecnológica de intermediación y conexión</strong> entre usuarios y conductores independientes. :entity_name <strong>no presta servicios de transporte</strong>, <strong>no opera vehículos</strong>, <strong>no emplea conductores</strong> ni actúa como empresa de transporte público, taxi, mensajería o carga.</p>
<p>El acuerdo de prestación del servicio de movilidad, encomienda, acarreo o recorrido compartido se celebra <strong>directamente entre el Usuario y el Conductor independiente</strong>. :brand_name facilita contacto, coordinación, pagos integrados y herramientas de seguridad.</p>

<h2 id="registro">3. Registro y cuenta</h2>
<p>Para usar :brand_name el Usuario debe ser mayor de edad (18 años), proporcionar información veraz y mantener la confidencialidad de sus credenciales. :entity_name puede solicitar verificación de identidad, teléfono o correo electrónico.</p>
<p>El Usuario es responsable de toda actividad realizada desde su cuenta. Notifique de inmediato accesos no autorizados a :support_email.</p>

<h2 id="servicios">4. Servicios disponibles</h2>
<p>La plataforma puede habilitar, según la ciudad y configuración operativa:</p>
<ul>
<li><strong>Viajes:</strong> conexión entre usuarios y conductores independientes para desplazamientos urbanos o intermunicipales acordados entre las partes.</li>
<li><strong>Encomiendas:</strong> envío de paquetes o documentos coordinado entre remitente, destinatario y conductor independiente.</li>
<li><strong>Acarreos:</strong> transporte de mercancías de mayor volumen bajo acuerdo directo entre las partes.</li>
<li><strong>Recorridos compartidos:</strong> publicación y aceptación de gastos compartidos del recorrido entre usuarios; no constituye tarifa comercial de transporte por parte de :brand_name.</li>
</ul>
<p>La disponibilidad de categorías (incluidas denominaciones como «Amarillo» u otras) no implica que :brand_name opere como transportista.</p>

<h2 id="tarifas">5. Tarifas, valores y aportes</h2>
<p>Los montos mostrados en la aplicación son <strong>valores o aportes propuestos o acordados entre Usuario y Conductor independiente</strong>, sujetos a negociación dentro de los parámetros de la plataforma. :brand_name puede aplicar tarifas de servicio, comisiones o cargos de procesamiento informados antes de confirmar la operación.</p>
<p>Impuestos, peajes y recargos adicionales acordados entre las partes son responsabilidad de quienes intervienen en el servicio, salvo disposición expresa en la app.</p>

<h2 id="pagos">6. Pagos</h2>
<p>Los pagos se procesan mediante proveedores integrados (p. ej. billetera digital, tarjeta u otros medios habilitados). :brand_name no garantiza la disponibilidad permanente de un método de pago específico.</p>
<p>Los reembolsos, devoluciones a billetera o ajustes se regirán por la política de cancelaciones vigente y las reglas del procesador de pagos aplicable.</p>

<h2 id="cancelaciones">7. Cancelaciones</h2>
<p>El Usuario y el Conductor independiente pueden cancelar solicitudes conforme a las reglas mostradas en la app al momento de la operación. Cancelaciones reiteradas o abusivas pueden generar advertencias, restricciones temporales o suspensión de la cuenta.</p>
<p>Cargos por cancelación tardía o no presentación, cuando apliquen, se informarán antes de confirmar el servicio.</p>

<h2 id="conductores-independientes">8. Conductores independientes</h2>
<p>Los conductores actúan por cuenta propia, sin relación laboral, subordinación, exclusividad ni representación comercial con :entity_name. Deben contar con licencia, documentación del vehículo, seguros y permisos exigidos por la normativa de tránsito y transporte vigente en :entity_country.</p>
<p>:brand_name puede verificar documentos, pero la responsabilidad final del cumplimiento normativo recae en el Conductor independiente.</p>

<h2 id="obligaciones">9. Obligaciones del Usuario</h2>
<p>El Usuario se obliga a: (i) usar la plataforma de forma lícita y respetuosa; (ii) no solicitar servicios ilícitos o peligrosos; (iii) proporcionar direcciones y datos de contacto correctos; (iv) cumplir las <a href=":public_url/legal/condiciones-usuario?lang=es">Condiciones para Usuarios</a>; (v) respetar a conductores y terceros; (vi) no manipular tarifas, identidades o ubicaciones con fines fraudulentos.</p>

<h2 id="limitacion-responsabilidad">10. Limitación de responsabilidad</h2>
<p>En la máxima medida permitida por la ley colombiana, :entity_name no será responsable por accidentes, retrasos, pérdidas, daños materiales o personales, disputas contractuales, incumplimientos o conductas derivadas de la interacción directa entre Usuario y Conductor independiente.</p>
<p>La plataforma se ofrece «tal cual», sin garantía de disponibilidad ininterrumpida, ausencia de errores o idoneidad para un fin particular.</p>

<h2 id="propiedad-intelectual">11. Propiedad intelectual</h2>
<p>Marcas, logotipos, software, diseños, bases de datos y contenidos de :brand_name son propiedad de :entity_name o sus licenciantes. Queda prohibida la reproducción, ingeniería inversa o explotación no autorizada.</p>

<h2 id="suspension">12. Suspensión y terminación</h2>
<p>:entity_name puede suspender o cerrar cuentas por incumplimiento de estos Términos, fraude, riesgo para la comunidad, requerimiento de autoridad o inactividad prolongada, previa comunicación cuando sea razonablemente posible.</p>

<h2 id="datos">13. Protección de datos personales</h2>
<p>El tratamiento de datos personales se rige por la <a href=":public_url/legal/privacidad?lang=es">Política de Privacidad</a> y la <a href=":public_url/legal/tratamiento-datos?lang=es">Política de Tratamiento de Datos</a>. Contacto de privacidad: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="ley-colombiana">14. Ley aplicable y jurisdicción</h2>
<p>Estos Términos se interpretan conforme a las leyes de la República de Colombia. Las controversias se someterán a los jueces competentes de :entity_country, sin perjuicio de mecanismos conciliatorios o PQR previstos en la ley.</p>

<h2 id="pqr">15. Peticiones, quejas y reclamos (PQR)</h2>
<p>Para presentar PQR relacionadas con la plataforma, consulte el documento de <a href=":public_url/legal/pqr?lang=es">PQR</a> o escriba a <a href="mailto::pqr_email">:pqr_email</a>. :entity_name atenderá conforme a la Ley 1480 de 2011 y plazos aplicables.</p>

<h2 id="modificaciones">16. Modificaciones</h2>
<p>:entity_name puede actualizar estos Términos. La versión vigente se publicará con identificador :consent_version y fecha :last_updated. El uso continuado de :brand_name tras la publicación de cambios materiales implica aceptación, salvo derechos irrenunciables del consumidor.</p>

<p><strong>Contacto legal:</strong> <a href="mailto::legal_email">:legal_email</a> · <strong>Soporte:</strong> <a href="mailto::support_email">:support_email</a></p>
</div>
HTML;
    }

    private static function terminosEnBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Version:</strong> :consent_version · <strong>Last updated:</strong> :last_updated · <strong>Operator:</strong> :entity_name (NIT :entity_nit)</p>

<h2 id="purpose">1. Purpose</h2>
<p>These Terms and Conditions govern access to and use of the <strong>:brand_name</strong> digital platform at <a href=":public_url">:public_url</a> and associated mobile applications, operated by <strong>:entity_name</strong>, located at :entity_address, :entity_country.</p>
<p>By registering or using :brand_name, you accept these Terms, the <a href=":public_url/legal/privacidad?lang=en">Privacy Policy</a>, and applicable user or driver conditions.</p>

<h2 id="platform-nature">2. Platform nature — :brand_name is NOT a carrier</h2>
<p><strong>:brand_name is a technology platform that connects users with independent drivers.</strong> :entity_name <strong>does not provide transport services</strong>, <strong>does not operate vehicles</strong>, and <strong>is not the employer of drivers</strong>.</p>
<p>Any mobility, delivery, haulage, or shared-ride agreement is made <strong>directly between the user and the independent driver</strong>. :brand_name provides connection, coordination, integrated payments, and safety tools.</p>

<h2 id="registration">3. Registration</h2>
<p>Users must be at least 18 years old, provide accurate information, and safeguard account credentials. Notify unauthorized access to <a href="mailto::support_email">:support_email</a>.</p>

<h2 id="services">4. Available services</h2>
<p>Depending on city and configuration, the platform may enable trips, parcel deliveries, haulage, and shared rides where users agree on contributions or shared expenses. Category labels in the app do not mean :brand_name operates as a public carrier.</p>

<h2 id="fares">5. Fares and contributions</h2>
<p>Amounts shown are values or contributions proposed or agreed between users and independent drivers. :brand_name may charge service or processing fees disclosed before confirmation.</p>

<h2 id="payments">6. Payments</h2>
<p>Payments are processed through integrated providers. Refunds and wallet adjustments follow the cancellation policy and payment processor rules in effect at the time of the transaction.</p>

<h2 id="cancellations">7. Cancellations</h2>
<p>Users and drivers may cancel according to in-app rules. Repeated abusive cancellations may lead to restrictions or account suspension.</p>

<h2 id="independent-drivers">8. Independent drivers</h2>
<p>Drivers act on their own account without employment relationship with :entity_name. They must maintain valid licenses, vehicle documentation, and compliance with traffic and transport law in :entity_country.</p>

<h2 id="obligations">9. User obligations</h2>
<p>Users must use the platform lawfully, provide accurate trip details, respect drivers and third parties, and refrain from fraud or manipulation of fares or location data.</p>

<h2 id="liability">10. Limitation of liability</h2>
<p>To the fullest extent permitted by Colombian law, :entity_name is not liable for accidents, delays, losses, or disputes arising from direct interactions between users and independent drivers. The platform is provided «as is».</p>

<h2 id="intellectual-property">11. Intellectual property</h2>
<p>Trademarks, software, and content of :brand_name belong to :entity_name or its licensors. Unauthorized reproduction or reverse engineering is prohibited.</p>

<h2 id="suspension">12. Suspension</h2>
<p>:entity_name may suspend or terminate accounts for breach of these Terms, fraud, safety risks, authority requests, or prolonged inactivity when reasonable notice is possible.</p>

<h2 id="data">13. Personal data</h2>
<p>Processing is governed by our <a href=":public_url/legal/privacidad?lang=en">Privacy Policy</a>. Contact: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="colombian-law">14. Governing law</h2>
<p>These Terms are governed by the laws of the Republic of Colombia. Disputes are subject to competent courts in :entity_country, without prejudice to PQR and conciliation mechanisms.</p>

<h2 id="pqr">15. Petitions, complaints, and claims</h2>
<p>See our <a href=":public_url/legal/pqr?lang=en">PQR page</a> or email <a href="mailto::pqr_email">:pqr_email</a>.</p>

<h2 id="changes">16. Changes</h2>
<p>:entity_name may update these Terms. The current version is :consent_version, last updated :last_updated. Continued use after material changes constitutes acceptance where permitted by law.</p>

<p><strong>Legal:</strong> <a href="mailto::legal_email">:legal_email</a> · <strong>Support:</strong> <a href="mailto::support_email">:support_email</a></p>
</div>
HTML;
    }

    private static function privacidadEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated</p>

<h2 id="responsable">1. Responsable del tratamiento</h2>
<p><strong>:entity_name</strong> (NIT :entity_nit), con domicilio en :entity_address, :entity_country, es responsable del tratamiento de datos personales recolectados a través de :brand_name. Contacto Habeas Data: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="datos">2. Datos personales tratados</h2>
<ul>
<li>Identificación y contacto: nombre, documento, teléfono, correo, foto de perfil.</li>
<li>Datos de conductor independiente y vehículo: licencias, SOAT, tarjeta de propiedad, revisiones.</li>
<li><strong>Ubicación</strong> en primer y segundo plano (conductores): conexión de solicitudes, coordinación de recorridos, seguridad y disponibilidad.</li>
<li>Datos de uso, dispositivo, registros de viajes, pagos, calificaciones y comunicaciones.</li>
<li>Datos de soporte, PQR y verificación de identidad.</li>
</ul>

<h2 id="finalidades">3. Finalidades del tratamiento</h2>
<p>Crear y administrar cuentas; conectar usuarios y conductores independientes; procesar pagos y billetera; prevenir fraude; mejorar la plataforma; cumplir obligaciones legales; atender PQR; y, con consentimiento previo, enviar comunicaciones comerciales.</p>

<h2 id="bases-legales">4. Bases legales</h2>
<p>Autorización del titular (Ley 1581 de 2012), ejecución de la relación con el usuario, interés legítimo en seguridad y mejora del servicio, y cumplimiento de deberes legales en :entity_country.</p>

<h2 id="derechos-arco">5. Derechos ARCO y del titular</h2>
<p>Usted puede acceder, rectificar, actualizar, suprimir datos y revocar la autorización, así como presentar quejas ante la Superintendencia de Industria y Comercio (SIC). Solicitudes: <a href="mailto::privacy_email">:privacy_email</a>, indicando identificación y descripción clara del derecho a ejercer.</p>

<h2 id="retencion">6. Retención</h2>
<p>Conservamos datos mientras exista relación con el titular, sea necesario para finalidades informadas o exista obligación legal de conservación (contable, fiscal, probatoria o PQR). Posteriormente se eliminarán o anonimizarán conforme a políticas internas.</p>

<h2 id="transferencias">7. Transferencias y encargados</h2>
<p>Podemos compartir datos con proveedores que actúan como encargados (mapas, notificaciones push, pagos, nube, analítica, soporte), bajo contratos que exigen confidencialidad y seguridad. Transferencias internacionales se realizarán con garantías aplicables.</p>

<h2 id="seguridad">8. Seguridad</h2>
<p>Implementamos medidas técnicas, administrativas y humanas razonables para proteger datos personales. Ningún sistema es 100 % infalible; reporte incidentes sospechosos a :privacy_email.</p>

<h2 id="menores">9. Menores de edad</h2>
<p>:brand_name no está dirigida a menores de 18 años. Si detectamos datos de menores sin autorización válida, procederemos a su eliminación.</p>

<h2 id="cambios">10. Cambios a esta política</h2>
<p>Publicaremos actualizaciones con versión :consent_version y fecha :last_updated. Cambios materiales se comunicarán por medios razonables (app, correo o aviso en sitio).</p>

<h2 id="contacto">11. Contacto</h2>
<p><strong>Privacidad:</strong> <a href="mailto::privacy_email">:privacy_email</a> · <strong>Soporte:</strong> <a href="mailto::support_email">:support_email</a> · <strong>PQR:</strong> <a href="mailto::pqr_email">:pqr_email</a></p>
<p>Política detallada de tratamiento: <a href=":public_url/legal/tratamiento-datos?lang=es">Política de Tratamiento de Datos Personales</a>.</p>
</div>
HTML;
    }

    private static function privacidadEnBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Version:</strong> :consent_version · <strong>Last updated:</strong> :last_updated</p>

<h2 id="controller">1. Data controller</h2>
<p><strong>:entity_name</strong> (NIT :entity_nit), :entity_address, :entity_country, is the controller of personal data collected through :brand_name. Privacy contact: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="data">2. Personal data we process</h2>
<p>Identification and contact data; independent driver and vehicle documentation; location (including background location for drivers); trip, payment, and usage records; support and PQR communications; and device information.</p>

<h2 id="purposes">3. Purposes</h2>
<p>Account management, connecting users and independent drivers, payments and wallet, fraud prevention, platform improvement, legal compliance, customer support, and—with prior consent—marketing communications.</p>

<h2 id="legal-bases">4. Legal bases</h2>
<p>Consent under Colombian Law 1581 of 2012, contractual necessity, legitimate interests in security and service improvement, and legal obligations in :entity_country.</p>

<h2 id="rights">5. Data-subject rights</h2>
<p>You may access, rectify, update, delete, and withdraw consent, and file complaints with the Superintendence of Industry and Commerce (SIC). Requests: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="retention">6. Retention</h2>
<p>We retain data while the relationship exists, as needed for stated purposes, or as required by law, then delete or anonymize according to internal policies.</p>

<h2 id="transfers">7. Processors and transfers</h2>
<p>We share data with service providers (maps, push notifications, payments, cloud, analytics) under confidentiality and security agreements. International transfers use applicable safeguards.</p>

<h2 id="security">8. Security</h2>
<p>We implement reasonable technical and organizational measures. Report suspected incidents to :privacy_email.</p>

<h2 id="minors">9. Minors</h2>
<p>:brand_name is not directed to persons under 18. We will delete unauthorized minor data when detected.</p>

<h2 id="changes">10. Changes</h2>
<p>Updates are published with version :consent_version and date :last_updated.</p>

<h2 id="contact">11. Contact</h2>
<p><a href="mailto::privacy_email">:privacy_email</a> · <a href="mailto::support_email">:support_email</a> · <a href=":public_url/legal/tratamiento-datos?lang=en">Data Processing Policy</a></p>
</div>
HTML;
    }

    private static function tratamientoDatosEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated · <strong>Marco:</strong> Ley 1581 de 2012</p>

<h2 id="responsable">1. Responsable y domicilio</h2>
<p><strong>:entity_name</strong>, NIT :entity_nit, :entity_address, :entity_country. Canal de atención al titular: <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="alcance">2. Alcance</h2>
<p>Esta política aplica al tratamiento de datos personales de usuarios, conductores independientes, visitantes del sitio web y personas que contacten canales oficiales de :brand_name.</p>

<h2 id="categorias">3. Categorías de datos</h2>
<p>Datos de identificación, contacto, financieros y transaccionales, geolocalización, biométricos básicos (foto), datos de vehículo, logs técnicos y metadatos de comunicaciones.</p>

<h2 id="finalidades">4. Finalidades específicas</h2>
<p>Registro; verificación KYC; emparejamiento de solicitudes; facturación electrónica cuando aplique; prevención de lavado de activos y fraude; estadísticas agregadas; cumplimiento de órdenes de autoridad competente.</p>

<h2 id="autorizacion">5. Autorización y revocatoria</h2>
<p>El titular autoriza el tratamiento al aceptar términos y formularios informativos. Puede revocar la autorización cuando no exista deber legal o contractual que lo impida, mediante solicitud a :privacy_email.</p>

<h2 id="derechos">6. Derechos del titular</h2>
<p>Conocer, actualizar, rectificar y suprimir datos; solicitar prueba de la autorización; ser informado sobre uso; presentar quejas ante :entity_name y la SIC.</p>

<h2 id="procedimiento">7. Procedimiento de consultas</h2>
<p>Las solicitudes se atenderán en máximo quince (15) días hábiles, prorrogables por causa justificada. Se podrá solicitar información adicional para verificar identidad.</p>

<h2 id="reclamos">8. Procedimiento de reclamos</h2>
<p>Reclamos por infracción al régimen de Habeas Data se resolverán en máximo quince (15) días hábiles desde el día siguiente a su recepción, conforme al artículo 15 de la Ley 1581 de 2012.</p>

<h2 id="encargados">9. Encargados del tratamiento</h2>
<p>Proveedores de infraestructura, mensajería, mapas, pagos y analítica actúan como encargados con obligaciones contractuales de confidencialidad, uso limitado y seguridad.</p>

<h2 id="transferencias">10. Transferencia a terceros países</h2>
<p>Cuando exista transferencia internacional, :entity_name verificará que el país o el receptor ofrezca niveles adecuados de protección o se suscriban cláusulas contractuales aplicables.</p>

<h2 id="vigencia">11. Vigencia del archivo</h2>
<p>Los datos se conservarán durante la relación comercial y plazos legales posteriores. Criterios de supresión están documentados en el manual interno de :entity_name.</p>

<h2 id="actualizacion">12. Actualización</h2>
<p>Versión :consent_version, publicada el :last_updated. Consulte también la <a href=":public_url/legal/privacidad?lang=es">Política de Privacidad</a>.</p>
</div>
HTML;
    }

    private static function condicionesUsuarioEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated</p>

<h2 id="aceptacion">1. Aceptación</h2>
<p>Al solicitar un servicio como pasajero o remitente en :brand_name, usted acepta estas condiciones complementarias a los <a href=":public_url/legal/terminos?lang=es">Términos y Condiciones</a> generales.</p>

<h2 id="elegibilidad">2. Elegibilidad</h2>
<p>Debe ser mayor de edad, contar con medio de pago válido cuando aplique y no estar suspendido de la plataforma.</p>

<h2 id="solicitud">3. Solicitud de servicios</h2>
<p>Proporcione origen, destino y datos de contacto veraces. Para encomiendas y acarreos, declare contenido permitido y peso/volumen aproximado. Está prohibido solicitar transporte de sustancias ilícitas, armas o mercancía peligrosa no declarada.</p>

<h2 id="tarifas">4. Valores y pagos</h2>
<p>El valor mostrado es propuesta negociable con el conductor independiente. Confirme el monto antes de iniciar el servicio. Pagos en app quedan registrados en historial y billetera cuando corresponda.</p>

<h2 id="conducta">5. Conducta y seguridad</h2>
<p>Trate con respeto al conductor y terceros. Use cinturón de seguridad. Comparta estado del viaje con contactos de confianza. Ante emergencia, use SOS y autoridades locales.</p>

<h2 id="cancelaciones">6. Cancelaciones y no show</h2>
<p>Las reglas de cancelación se muestran antes de confirmar. Cancelaciones injustificadas reiteradas pueden limitar el acceso a la plataforma.</p>

<h2 id="calificaciones">7. Calificaciones y reportes</h2>
<p>Las calificaciones deben ser objetivas y veraces. Reporte incidentes por la app o a :support_email con el mayor detalle posible.</p>

<h2 id="responsabilidad">8. Responsabilidad del usuario</h2>
<p>El acuerdo de servicio es con el conductor independiente. :brand_name no garantiza tiempos de llegada ni ausencia de incidentes en la interacción directa entre las partes.</p>

<h2 id="contacto">9. Contacto</h2>
<p>Soporte: <a href="mailto::support_email">:support_email</a> · PQR: <a href="mailto::pqr_email">:pqr_email</a></p>
</div>
HTML;
    }

    private static function condicionesConductorEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated</p>

<h2 id="independencia">1. Independencia contractual</h2>
<p>El conductor presta servicios por cuenta propia. No existe relación laboral, salarial ni de subordinación con :entity_name. Usted define su disponibilidad y acepta solicitudes conforme a la normativa aplicable en :entity_country.</p>

<h2 id="documentos">2. Documentación requerida</h2>
<p>Licencia de conducción vigente, documento de identidad, SOAT, revisión técnico-mecánica, tarjeta de propiedad o autorización del vehículo, pólizas exigidas y fotografías actualizadas. :brand_name puede suspender la cuenta si la documentación expira o es inconsistente.</p>

<h2 id="vehiculo">3. Vehículo y categorías</h2>
<p>El vehículo debe cumplir estándares mínimos de la categoría seleccionada (viaje, encomienda, acarreo, compartido). Usted es responsable del mantenimiento y legalidad del vehículo.</p>

<h2 id="comisiones">4. Comisiones y tarifas de servicio</h2>
<p>:brand_name puede retener comisión o tarifa de plataforma sobre operaciones completadas, informada en la app antes de aceptar viajes. Impuestos derivados de su actividad independiente son su responsabilidad.</p>

<h2 id="wallet">5. Billetera y pagos</h2>
<p>Los ingresos pueden acreditarse en billetera digital dentro de la app. Retiros, topes, plazos y métodos de desembolso se rigen por las reglas publicadas en el panel del conductor y proveedores de pago integrados.</p>

<h2 id="obligaciones">6. Obligaciones del conductor</h2>
<p>Cumplir ley de tránsito; no conducir bajo influencia de alcohol o sustancias; respetar usuarios; no solicitar pagos fuera de canal cuando la app exija pago electrónico; mantener ubicación activa durante servicios activos según configuración.</p>

<h2 id="suspension">7. Suspensión y desactivación</h2>
<p>Fraude, documentos falsos, calificaciones críticas reiteradas, violencia, discriminación o incumplimiento grave pueden causar desactivación temporal o permanente.</p>

<h2 id="seguros">8. Seguros e incidentes</h2>
<p>Mantenga coberturas exigidas por ley. Reporte accidentes a autoridades y notifique a :support_email con soporte documental cuando aplique.</p>

<h2 id="contacto">9. Contacto</h2>
<p>Soporte conductores: <a href="mailto::support_email">:support_email</a> · Legal: <a href="mailto::legal_email">:legal_email</a></p>
</div>
HTML;
    }

    private static function avisoLegalEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Última actualización:</strong> :last_updated</p>

<h2 id="identificacion">1. Identificación del titular del sitio</h2>
<p><strong>Razón social:</strong> :entity_name<br>
<strong>NIT:</strong> :entity_nit<br>
<strong>Domicilio:</strong> :entity_address, :entity_country<br>
<strong>Sitio web:</strong> <a href=":public_url">:public_url</a><br>
<strong>Correo legal:</strong> <a href="mailto::legal_email">:legal_email</a></p>

<h2 id="objeto-sitio">2. Objeto del sitio y aplicaciones</h2>
<p>El sitio y las aplicaciones :brand_name ofrecen información corporativa, acceso a la plataforma de conexión entre usuarios y conductores independientes, y documentación legal. :entity_name no presta servicios de transporte.</p>

<h2 id="propiedad">3. Propiedad intelectual e industrial</h2>
<p>Contenidos, marcas, logotipos, código fuente, diseños y bases de datos están protegidos por la legislación colombiana e internacional. Queda prohibida su reproducción total o parcial sin autorización escrita de :entity_name.</p>

<h2 id="enlaces">4. Enlaces externos</h2>
<p>Enlaces a sitios de terceros se ofrecen por conveniencia. :entity_name no controla ni respalda contenidos ajenos; el acceso es bajo responsabilidad del usuario.</p>

<h2 id="limitacion">5. Limitación de responsabilidad</h2>
<p>:entity_name no garantiza ausencia de interrupciones o errores en el sitio. No responde por daños derivados del uso de información publicada ni de servicios prestados directamente por conductores independientes conectados a través de la plataforma.</p>

<h2 id="ley">6. Ley aplicable</h2>
<p>El presente aviso se rige por las leyes de Colombia. Para tratamiento de datos consulte <a href=":public_url/legal/privacidad?lang=es">Privacidad</a> y <a href=":public_url/legal/tratamiento-datos?lang=es">Tratamiento de datos</a>.</p>
</div>
HTML;
    }

    private static function avisoLegalEnBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Last updated:</strong> :last_updated</p>

<h2 id="identification">1. Site owner identification</h2>
<p><strong>Company:</strong> :entity_name<br>
<strong>Tax ID (NIT):</strong> :entity_nit<br>
<strong>Address:</strong> :entity_address, :entity_country<br>
<strong>Website:</strong> <a href=":public_url">:public_url</a><br>
<strong>Legal email:</strong> <a href="mailto::legal_email">:legal_email</a></p>

<h2 id="purpose">2. Purpose of the site and apps</h2>
<p>The :brand_name website and applications provide corporate information, access to a connection platform between users and independent drivers, and legal documentation. :entity_name does not provide transport services.</p>

<h2 id="ip">3. Intellectual and industrial property</h2>
<p>Content, trademarks, logos, source code, designs, and databases are protected under Colombian and international law. Reproduction without written authorization from :entity_name is prohibited.</p>

<h2 id="links">4. External links</h2>
<p>Links to third-party sites are provided for convenience. :entity_name does not control external content; access is at the user's own risk.</p>

<h2 id="liability">5. Limitation of liability</h2>
<p>:entity_name does not guarantee uninterrupted or error-free operation of the site. It is not liable for damages from use of published information or for services provided directly by independent drivers connected through the platform.</p>

<h2 id="law">6. Governing law</h2>
<p>This notice is governed by Colombian law. See <a href=":public_url/legal/privacidad?lang=en">Privacy</a> and <a href=":public_url/legal/tratamiento-datos?lang=en">Data Processing</a> policies.</p>
</div>
HTML;
    }

    private static function seguridadEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Última actualización:</strong> :last_updated</p>

<h2 id="compromiso">1. Compromiso de seguridad</h2>
<p>:brand_name implementa herramientas para reducir riesgos en la interacción entre usuarios y conductores independientes. La seguridad es responsabilidad compartida: use el sentido común y cumpla la ley.</p>

<h2 id="sos">2. Botón SOS y emergencias</h2>
<p>En situación de riesgo inminente, <strong>llame primero a las autoridades locales (123 en Colombia)</strong>. Luego active SOS en la app para notificar contactos de confianza y registrar el evento. El SOS no reemplaza servicios de emergencia.</p>

<h2 id="verificacion">3. Verificación de identidad</h2>
<p>Usuarios y conductores pueden someterse a verificación de teléfono, correo, documento y selfie. Compare foto y datos del vehículo antes de iniciar un servicio.</p>

<h2 id="compartir-viaje">4. Compartir estado del viaje</h2>
<p>Comparta enlace o estado del recorrido con familiares o contactos de confianza desde la aplicación durante servicios activos.</p>

<h2 id="buenas-practicas-usuario">5. Buenas prácticas para usuarios</h2>
<ul>
<li>Suba y baje en puntos seguros y visibles.</li>
<li>Verifique placa, modelo y nombre del conductor.</li>
<li>No comparta datos bancarios completos con terceros.</li>
<li>Reporte comportamiento inapropiado de inmediato.</li>
</ul>

<h2 id="buenas-practicas-conductor">6. Buenas prácticas para conductores</h2>
<ul>
<li>Mantenga vehículo en buen estado y documentación al día.</li>
<li>No conduzca fatigado o bajo efectos de alcohol o drogas.</li>
<li>Respete rutas acordadas y privacidad del usuario.</li>
<li>Use casco o elementos de protección cuando la categoría lo exija.</li>
</ul>

<h2 id="reportes">7. Reportes y seguimiento</h2>
<p>Incidentes de seguridad: <a href="mailto::support_email">:support_email</a>. Casos graves pueden escalarse al equipo legal en <a href="mailto::legal_email">:legal_email</a>.</p>
</div>
HTML;
    }

    private static function faqEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Última actualización:</strong> :last_updated</p>

<h2 id="general">General</h2>

<h3 id="faq-que-es">¿Qué es :brand_name?</h3>
<p>Plataforma tecnológica que conecta usuarios con conductores independientes para viajes, encomiendas, acarreos y recorridos compartidos en :entity_country.</p>

<h3 id="faq-transportista">¿:brand_name es una empresa de transporte?</h3>
<p>No. :entity_name no opera vehículos ni presta servicios de transporte público, taxi o mensajería como transportista.</p>

<h3 id="faq-ciudades">¿En qué ciudades opera?</h3>
<p>La disponibilidad depende de la expansión operativa. Consulte la app para servicios activos en su municipio.</p>

<h2 id="tarifas-pagos">Tarifas y pagos</h2>

<h3 id="faq-valor">¿Cómo se define el valor del servicio?</h3>
<p>El usuario propone un valor o aporte; el conductor independiente puede aceptar o contraofertar según reglas de la app.</p>

<h3 id="faq-compartido">¿Qué es un recorrido compartido?</h3>
<p>Modalidad donde usuarios acuerdan compartir gastos del recorrido. No es tarifa comercial de transporte fijada por :brand_name.</p>

<h3 id="faq-metodos-pago">¿Qué métodos de pago hay?</h3>
<p>Dependen de la configuración local: billetera, tarjeta u otros medios integrados mostrados al confirmar.</p>

<h2 id="conductores">Conductores</h2>

<h3 id="faq-conductor-empleado">¿El conductor trabaja para :brand_name?</h3>
<p>No. Actúa como conductor independiente sin relación laboral con :entity_name.</p>

<h3 id="faq-ser-conductor">¿Cómo me registro como conductor?</h3>
<p>Descargue la app, elija perfil conductor y cargue documentos requeridos. El equipo revisará la solicitud.</p>

<h2 id="ubicacion-datos">Ubicación y datos</h2>

<h3 id="faq-ubicacion">¿Por qué solicitan mi ubicación?</h3>
<p>Para conectar solicitudes cercanas, mostrar ETA, coordinar recorridos y, en conductores activos, mantener disponibilidad y seguridad.</p>

<h3 id="faq-privacidad">¿Cómo ejerco derechos sobre mis datos?</h3>
<p>Escriba a <a href="mailto::privacy_email">:privacy_email</a>. Detalle en <a href=":public_url/legal/privacidad?lang=es">Política de Privacidad</a>.</p>

<h2 id="soporte">Soporte y PQR</h2>

<h3 id="faq-soporte">¿Cómo contacto soporte?</h3>
<p>Chat en la app o correo <a href="mailto::support_email">:support_email</a>.</p>

<h3 id="faq-pqr">¿Cómo presento una queja formal?</h3>
<p>Vea <a href=":public_url/legal/pqr?lang=es">PQR</a> o escriba a <a href="mailto::pqr_email">:pqr_email</a>.</p>

<h3 id="faq-cuenta">¿Cómo elimino mi cuenta?</h3>
<p>Use el flujo de eliminación en la app o la página <a href=":public_url/legal/eliminar-cuenta">Eliminar cuenta</a>, sujeto a retenciones legales.</p>
</div>
HTML;
    }

    private static function contactoEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Última actualización:</strong> :last_updated</p>

<h2 id="entidad">1. Información de la entidad</h2>
<p><strong>:entity_name</strong><br>
NIT: :entity_nit<br>
Dirección: :entity_address, :entity_country<br>
Sitio: <a href=":public_url">:public_url</a><br>
Índice del centro legal: <a href=":centro_legal_url">:centro_legal_url</a></p>

<h2 id="canales">2. Canales oficiales</h2>
<ul>
<li><strong>Soporte general:</strong> <a href="mailto::support_email">:support_email</a></li>
<li><strong>Privacidad y Habeas Data:</strong> <a href="mailto::privacy_email">:privacy_email</a></li>
<li><strong>Asuntos legales:</strong> <a href="mailto::legal_email">:legal_email</a></li>
<li><strong>PQR (peticiones, quejas y reclamos):</strong> <a href="mailto::pqr_email">:pqr_email</a></li>
<li><strong>Chat en la app:</strong> menú de usuario → soporte / chat en vivo (cuando esté disponible).</li>
</ul>

<h2 id="horarios">3. Tiempos de respuesta</h2>
<p>Soporte general: intento de respuesta en 1–3 días hábiles. Solicitudes de datos personales y PQR: plazos legales aplicables (consulte <a href=":public_url/legal/pqr?lang=es">PQR</a> y <a href=":public_url/legal/tratamiento-datos?lang=es">Tratamiento de datos</a>).</p>

<h2 id="formulario">4. Formulario web</h2>
<p>También puede escribirnos desde el formulario de contacto en <a href=":public_url/#contact">:public_url/#contact</a>, aceptando términos y autorización de tratamiento de datos.</p>
</div>
HTML;
    }

    private static function pqrEsBody(): string
    {
        return <<<'HTML'
<div class="legal-prose">
<p class="legal-meta"><strong>Versión:</strong> :consent_version · <strong>Última actualización:</strong> :last_updated · <strong>Marco:</strong> Ley 1480 de 2011 (Estatuto del Consumidor)</p>

<h2 id="objeto">1. Objeto</h2>
<p>:entity_name pone a disposición de usuarios y consumidores el procedimiento de <strong>Peticiones, Quejas y Reclamos (PQR)</strong> relacionado con la plataforma :brand_name.</p>

<h2 id="definiciones">2. Definiciones</h2>
<ul>
<li><strong>Petición:</strong> solicitud de información, documentos o actuación sobre un asunto de interés.</li>
<li><strong>Queja:</strong> manifestación de insatisfacción por conducta del proveedor o prestación del servicio de plataforma.</li>
<li><strong>Reclamo:</strong> exigencia de corrección, restitución o compensación por incumplimiento.</li>
</ul>

<h2 id="canales">3. Canales de radicación</h2>
<ul>
<li>Correo: <a href="mailto::pqr_email">:pqr_email</a></li>
<li>Soporte en app con asunto «PQR»</li>
<li>Correo postal: :entity_address, :entity_country (referencia PQR :brand_name)</li>
</ul>

<h2 id="requisitos">4. Información mínima</h2>
<p>Nombre completo, tipo y número de identificación, correo y teléfono, descripción clara de hechos, fecha aproximada, ID de viaje o transacción si aplica, y pretensión concreta.</p>

<h2 id="plazos">5. Plazos de respuesta (Ley 1480 de 2011)</h2>
<p><strong>Peticiones:</strong> respuesta en un término máximo de quince (15) días hábiles.<br>
<strong>Quejas:</strong> respuesta en un término máximo de quince (15) días hábiles.<br>
<strong>Reclamos:</strong> respuesta en un término máximo de treinta (30) días hábiles, prorrogables hasta treinta (30) días más previa comunicación al consumidor.</p>
<p>El término corre a partir del día hábil siguiente a la radicación.</p>

<h2 id="acuse">6. Acuse de recibo</h2>
<p>Enviaremos acuse con número de radicado cuando sea posible. Conserve su referencia para seguimiento.</p>

<h2 id="escalamiento">7. Escalamiento</h2>
<p>Si no está conforme con la respuesta, puede acudir a mecanismos de conciliación o a la Superintendencia de Industria y Comercio conforme a la normativa de protección al consumidor en Colombia.</p>

<h2 id="datos">8. Tratamiento de datos en PQR</h2>
<p>Los datos suministrados se usarán exclusivamente para tramitar su solicitud, conforme a la <a href=":public_url/legal/privacidad?lang=es">Política de Privacidad</a>.</p>

<h2 id="contacto">9. Contacto PQR</h2>
<p><a href="mailto::pqr_email">:pqr_email</a> · Copia opcional a <a href="mailto::legal_email">:legal_email</a></p>
</div>
HTML;
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function cookies(string $lang): ?array
    {
        $isEn = $lang === 'en';

        return [
            'title' => $isEn ? 'Cookie Policy — :brand_name' : 'Política de Cookies — :brand_name',
            'summary' => $isEn
                ? 'How :brand_name uses cookies and similar technologies on the web portal and admin site.'
                : 'Uso de cookies y tecnologías similares en el portal web y sitio administrativo de :brand_name.',
            'body' => $isEn ? <<<'HTML'
<div class="legal-prose">
<h2 id="intro">1. What are cookies?</h2>
<p>Cookies are small files stored on your device when you visit our website. They help the site function, remember preferences, and—only with your consent—support analytics and marketing.</p>

<h2 id="types">2. Types we use</h2>
<ul>
<li><strong>Strictly necessary:</strong> session, security, consent storage. Always active.</li>
<li><strong>Functional:</strong> language preference, UI settings.</li>
<li><strong>Analytics (optional):</strong> Google Analytics, Microsoft Clarity—loaded only if you accept.</li>
<li><strong>Marketing (optional):</strong> Meta Pixel, Google Ads—loaded only if you accept.</li>
</ul>

<h2 id="manage">3. How to manage</h2>
<p>Use the cookie banner on first visit, clear browser cookies, or contact <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="third">4. Third parties</h2>
<p>Third-party providers may process data under their own policies when optional cookies are enabled.</p>

<h2 id="updates">5. Updates</h2>
<p>Version :consent_version · Last updated :last_updated.</p>
</div>
HTML
            : <<<'HTML'
<div class="legal-prose">
<h2 id="intro">1. ¿Qué son las cookies?</h2>
<p>Las cookies son archivos que se almacenan en su dispositivo al visitar nuestro sitio. Permiten el funcionamiento, recordar preferencias y—solo con su consentimiento—habilitar analítica y marketing.</p>

<h2 id="types">2. Tipos que utilizamos</h2>
<ul>
<li><strong>Estrictamente necesarias:</strong> sesión, seguridad, almacenamiento de consentimiento. Siempre activas.</li>
<li><strong>Funcionales:</strong> idioma, preferencias de interfaz.</li>
<li><strong>Analítica (opcional):</strong> Google Analytics, Microsoft Clarity—solo si acepta.</li>
<li><strong>Marketing (opcional):</strong> Meta Pixel, Google Ads—solo si acepta.</li>
</ul>

<h2 id="manage">3. Cómo gestionarlas</h2>
<p>Use el banner al ingresar, borre cookies del navegador o escriba a <a href="mailto::privacy_email">:privacy_email</a>.</p>

<h2 id="third">4. Terceros</h2>
<p>Proveedores externos pueden tratar datos conforme a sus políticas cuando usted habilita cookies opcionales.</p>

<h2 id="updates">5. Actualizaciones</h2>
<p>Versión :consent_version · Última actualización :last_updated.</p>
</div>
HTML,
        ];
    }

    /** @return array{title: string, summary: string, body: string}|null */
    private static function eliminarCuenta(string $lang): ?array
    {
        $isEn = $lang === 'en';

        return [
            'title' => $isEn ? 'Account Deletion — :brand_name' : 'Eliminación de cuenta — :brand_name',
            'summary' => $isEn
                ? 'How to request deletion of your :brand_name account and what happens to your data.'
                : 'Cómo solicitar la eliminación de su cuenta :brand_name y qué ocurre con sus datos.',
            'body' => $isEn ? <<<'HTML'
<div class="legal-prose">
<h2 id="how">1. How to request</h2>
<p>From the app: Profile → Settings → Delete account. Or use the <a href=":public_url/account-deletion/login">web deletion flow</a>.</p>

<h2 id="removed">2. Data removed or anonymized</h2>
<p>Profile, preferences, credentials, and non-mandatory history linked to your account will be deleted or anonymized.</p>

<h2 id="retained">3. Data we may retain</h2>
<p>Billing, fraud prevention, dispute resolution, and legal obligations may require retention for statutory periods under Colombian law.</p>

<h2 id="timeline">4. Timeline</h2>
<p>Up to 30 business days from confirmed request, unless a shorter period is technically feasible.</p>

<h2 id="contact">5. Privacy contact</h2>
<p><a href="mailto::privacy_email">:privacy_email</a></p>
</div>
HTML
            : <<<'HTML'
<div class="legal-prose">
<h2 id="how">1. Cómo solicitarlo</h2>
<p>Desde la app: Perfil → Configuración → Eliminar cuenta. También puede usar el <a href=":public_url/account-deletion/login">flujo web de eliminación</a>.</p>

<h2 id="removed">2. Datos eliminados o anonimizados</h2>
<p>Perfil, preferencias, credenciales e historial no obligatorio vinculado a su cuenta.</p>

<h2 id="retained">3. Datos que podemos conservar</h2>
<p>Facturación, prevención de fraude, disputas y obligaciones legales conforme a plazos legales en Colombia.</p>

<h2 id="timeline">4. Plazo</h2>
<p>Hasta 30 días hábiles desde la confirmación de la solicitud, salvo plazo menor técnicamente viable.</p>

<h2 id="contact">5. Contacto privacidad</h2>
<p><a href="mailto::privacy_email">:privacy_email</a></p>
<p class="mt-4"><a class="legal-btn" href=":public_url/account-deletion/login">Iniciar eliminación de cuenta</a></p>
</div>
HTML,
        ];
    }
}
