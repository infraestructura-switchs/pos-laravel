<a href="#" {{ $attributes->merge(['title' => '']) }}>
  <svg {{ $attributes->merge(['class' => '']) }} version="1.0" xmlns="http://www.w3.org/2000/svg" width="96.000000pt" height="96.000000pt"
    viewBox="0 0 96.000000 96.000000" preserveAspectRatio="xMidYMid meet">

    <g transform="translate(48.000000,48.000000) scale(1.000000,1.000000)" fill="currentColor" stroke="none">
      <!-- Forma principal de hoja/lágrima -->
      <path d="M0,-40 C-15,-40 -25,-25 -25,-10 C-25,5 -15,20 0,40 C15,20 25,5 25,-10 C25,-25 15,-40 0,-40 Z" 
            fill="#1E40AF" />
      
      <!-- División superior izquierda (verde azulado claro) -->
      <path d="M0,-40 C-10,-40 -15,-30 -15,-15 C-15,0 -10,10 0,20 L0,-40 Z" 
            fill="#14B8A6" />
      
      <!-- División superior derecha (azul oscuro) -->
      <path d="M0,-40 C10,-40 15,-30 15,-15 C15,0 10,10 0,20 L0,-40 Z" 
            fill="#1E3A8A" />
      
      <!-- División inferior izquierda (verde azulado más oscuro) -->
      <path d="M0,20 C-10,10 -15,0 -15,-15 C-15,-30 -10,-40 0,-40 L0,40 C-15,20 -10,5 -5,0 Z" 
            fill="#0F766E" />
      
      <!-- División inferior derecha (azul oscuro) -->
      <path d="M0,20 C10,10 15,0 15,-15 C15,-30 10,-40 0,-40 L0,40 C15,20 10,5 5,0 Z" 
            fill="#1E3A8A" />
      
      <!-- Líneas divisorias -->
      <path d="M0,-40 L0,40" stroke="#ffffff" stroke-width="2" fill="none" />
      <path d="M-25,-10 L25,-10" stroke="#ffffff" stroke-width="2" fill="none" />
    </g>
  </svg>
</a>
