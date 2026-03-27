@extends('layouts.app')

@section('title', 'Términos y Condiciones — ServiPueblo')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="text-center mb-6">
        <div class="text-4xl mb-3">📋</div>
        <h1 class="text-2xl font-bold text-gray-900">Términos y Condiciones de uso</h1>
        <p class="text-gray-500 mt-1 text-sm">Antes de continuar, necesitamos que leas y aceptes los siguientes términos.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-sm text-gray-700 leading-relaxed space-y-4 max-h-[60vh] overflow-y-auto" id="terms-box">

        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Última actualización: marzo 2026</p>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">1. Naturaleza del servicio</h2>
            <p>ServiPueblo es una plataforma de directorio digital que conecta a personas que ofrecen servicios, oficios o emprendimientos con potenciales clientes. ServiPueblo actúa exclusivamente como intermediario de contacto y <strong>no es parte de ninguna contratación, acuerdo comercial o transacción</strong> que se realice entre los usuarios.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">2. Responsabilidad por los servicios</h2>
            <p>ServiPueblo no garantiza la calidad, idoneidad, habilitación, seguridad ni legalidad de los servicios ofrecidos por los trabajadores o emprendimientos publicados. Cualquier contratación se realiza bajo exclusiva responsabilidad del usuario. ServiPueblo no responde por daños, perjuicios, incumplimientos, accidentes o cualquier consecuencia derivada de los servicios contratados a través de la plataforma.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">3. Veracidad de la información</h2>
            <p>El trabajador o emprendedor es el único responsable de la veracidad, exactitud y vigencia de la información publicada en su perfil (datos de contacto, descripción, fotos, etiquetas). ServiPueblo no verifica ni certifica la información proporcionada. La publicación de datos falsos, engañosos o que infrinjan derechos de terceros es responsabilidad exclusiva del usuario.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">4. Contenido publicado</h2>
            <p>Al registrarse, el usuario otorga a ServiPueblo una licencia no exclusiva para mostrar la información y las imágenes de su perfil dentro de la plataforma. ServiPueblo se reserva el derecho de rechazar, editar o eliminar cualquier perfil o contenido que considere inapropiado, falso o contrario a estos términos, sin necesidad de notificación previa ni expresión de causa.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">5. Datos personales</h2>
            <p>Los datos ingresados en la plataforma son utilizados exclusivamente para el funcionamiento del directorio. No se venden ni ceden a terceros. El usuario puede solicitar la eliminación de sus datos en cualquier momento escribiendo a <a href="mailto:servipueblosoporte@gmail.com" class="text-brand-600 hover:underline">servipueblosoporte@gmail.com</a>.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">6. Calificaciones y comentarios</h2>
            <p>Las calificaciones son realizadas por terceros y reflejan opiniones personales. ServiPueblo no se responsabiliza por el contenido de las mismas, aunque se reserva el derecho de eliminar aquellas que resulten ofensivas, falsas o malintencionadas.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">7. Limitación de responsabilidad</h2>
            <p>En ningún caso ServiPueblo, sus administradores o colaboradores serán responsables por daños directos, indirectos, incidentales o consecuentes que resulten del uso o la imposibilidad de uso de la plataforma.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">8. Modificaciones</h2>
            <p>ServiPueblo puede modificar estos términos en cualquier momento. Los usuarios registrados serán notificados y deberán aceptar los nuevos términos para continuar usando la plataforma.</p>
        </div>

        <div>
            <h2 class="font-semibold text-gray-900 mb-1">9. Jurisdicción</h2>
            <p>Ante cualquier disputa, las partes se someten a la jurisdicción de los tribunales ordinarios de la República Argentina.</p>
        </div>

    </div>

    {{-- Aceptar --}}
    <div class="mt-5 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form method="POST" action="{{ route('terms.accept') }}">
            @csrf
            <label class="flex items-start gap-3 cursor-pointer mb-4">
                <input type="checkbox" id="terms-check" required
                       class="mt-0.5 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-gray-700">
                    Leí y acepto los Términos y Condiciones de uso de ServiPueblo.
                </span>
            </label>
            <button type="submit" id="accept-btn"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 rounded-lg transition-colors">
                Aceptar y continuar
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
            @csrf
            <button type="submit" class="text-sm text-gray-400 hover:text-red-500 transition-colors">
                No acepto — cerrar sesión
            </button>
        </form>
    </div>

</div>
@endsection
