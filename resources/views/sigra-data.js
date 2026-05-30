/* ============================================================
   SIGRA — Capa de datos compartida (localStorage)
   Permite que mesa/cocina/facturación/crédito/etc. compartan estado.
   ============================================================ */
(function () {
  'use strict';

  const KEYS = {
    MESAS:     'sigra_mesas_v1',
    MENU:      'sigra_menu_v2',
    COCINA:    'sigra_cocina_v1',
    FACTURAS:  'sigra_facturas_v1',
    EMPRESAS:  'sigra_empresas_v1',
    ABONOS:    'sigra_abonos_v1',
    PROVEEDORES: 'sigra_proveedores_v1',
    CUENTAS_PROV: 'sigra_cuentas_prov_v1',
    PEDIDO_SEQ: 'sigra_pedido_seq_v1',
    PLANILLAS: 'sigra_planillas_v1',
    CONFIG: 'sigra_config_v1',
    CATEGORIAS: 'sigra_categorias_v1',
    EMPLEADOS: 'sigra_empleados_v1',
    ASISTENCIA: 'sigra_asistencia_v1',
    PERMISOS:   'sigra_permisos_v1',
  };

  /* ── Seeds ── */
  const SEED_MESAS = [
    { id:1,  numero:1,  capacidad:4, estado:'LIBRE',    pedido:null, reserva:null },
    { id:2,  numero:2,  capacidad:4, estado:'OCUPADA',  reserva:null,
      pedido:{ id:'PED-008', mesero:'Ana García', hora:'13:15', total:174.00,
        items:[
          { id:12, nombre:'Pasta Marinara', cantidad:2, precio:65.00, notas:'' },
          { id:5,  nombre:'Limonada',       cantidad:2, precio:22.00, notas:'Sin soda' },
        ]}},
    { id:3,  numero:3,  capacidad:4, estado:'OCUPADA',  reserva:null,
      pedido:{ id:'PED-001', mesero:'Pedro Caal', hora:'12:45', total:290.00,
        items:[
          { id:7, nombre:'Churrasco a la Parrilla', cantidad:2, precio:120.00, notas:'Término 3/4' },
          { id:3, nombre:'Jugo Natural',            cantidad:2, precio:25.00,  notas:'Naranja' },
        ]}},
    { id:4,  numero:4,  capacidad:4, estado:'LIBRE',    pedido:null, reserva:null },
    { id:5,  numero:5,  capacidad:6, estado:'LIBRE',    pedido:null, reserva:null },
    { id:6,  numero:6,  capacidad:6, estado:'RESERVADA', pedido:null,
      reserva:{ nombre:'Familia Hernández', telefono:'5555-1212', fecha:'2026-05-26', hora:'20:00', personas:6 }},
    { id:7,  numero:7,  capacidad:6, estado:'LIBRE',    pedido:null, reserva:null },
    { id:8,  numero:8,  capacidad:6, estado:'LIBRE',    pedido:null, reserva:null },
    { id:9,  numero:9,  capacidad:8, estado:'OCUPADA',  reserva:null,
      pedido:{ id:'PED-007', mesero:'José López', hora:'12:20', total:430.00,
        items:[
          { id:11, nombre:'Mariscos al Ajillo', cantidad:1, precio:145.00, notas:'' },
          { id:9,  nombre:'Caldo de Res',       cantidad:1, precio:75.00,  notas:'Sin chile' },
          { id:15, nombre:'Alitas de Pollo',    cantidad:1, precio:65.00,  notas:'Picante' },
          { id:6,  nombre:'Cerveza',            cantidad:4, precio:35.00,  notas:'Gallo' },
        ]}},
    { id:10, numero:10, capacidad:8, estado:'LIBRE',    pedido:null, reserva:null },
    { id:11, numero:11, capacidad:8, estado:'RESERVADA', pedido:null,
      reserva:{ nombre:'Carlos Mejía', telefono:'5555-3434', fecha:'2026-05-27', hora:'19:30', personas:8 }},
    { id:12, numero:12, capacidad:8, estado:'LIBRE',    pedido:null, reserva:null },
  ];

  const SEED_MENU = [
    /* Bebidas */
    { id:1,  categoria:'bebidas',  nombre:'Agua Pura',           precio:10.00, descripcion:'500ml',                       img:'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&fit=crop&auto=format&q=70' },
    { id:2,  categoria:'bebidas',  nombre:'Gaseosa',              precio:15.00, descripcion:'Coca-Cola, Pepsi',           img:'https://images.unsplash.com/photo-1624552184280-9e9631bbeee9?w=400&fit=crop&auto=format&q=70' },
    { id:3,  categoria:'bebidas',  nombre:'Jugo Natural',         precio:25.00, descripcion:'Naranja, Sandía, Mango',     img:'https://images.unsplash.com/photo-1546173159-315724a31696?w=400&fit=crop&auto=format&q=70' },
    { id:4,  categoria:'bebidas',  nombre:'Café',                 precio:20.00, descripcion:'Americano o con leche',      img:'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&fit=crop&auto=format&q=70' },
    { id:5,  categoria:'bebidas',  nombre:'Limonada',             precio:22.00, descripcion:'Con o sin soda',             img:'https://images.unsplash.com/photo-1497534446932-c925b458314e?w=400&fit=crop&auto=format&q=70' },
    { id:6,  categoria:'bebidas',  nombre:'Cerveza',              precio:35.00, descripcion:'Gallo o Dorada',             img:'https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=400&fit=crop&auto=format&q=70' },
    /* Platos */
    { id:7,  categoria:'platos',   nombre:'Churrasco a la Parrilla', precio:120.00, descripcion:'Con papas y ensalada',   img:'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400&fit=crop&auto=format&q=70' },
    { id:8,  categoria:'platos',   nombre:'Filete de Mojarra',     precio:95.00,  descripcion:'Frito o a la plancha',     img:'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&fit=crop&auto=format&q=70' },
    { id:9,  categoria:'platos',   nombre:'Caldo de Res',          precio:75.00,  descripcion:'Con verduras de temporada',img:'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&fit=crop&auto=format&q=70' },
    { id:10, categoria:'platos',   nombre:'Pollo a la Plancha',    precio:85.00,  descripcion:'Con arroz y frijoles',     img:'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=400&fit=crop&auto=format&q=70' },
    { id:11, categoria:'platos',   nombre:'Mariscos al Ajillo',    precio:145.00, descripcion:'Camarones y calamar',      img:'https://images.unsplash.com/photo-1559847844-5315695dadae?w=400&fit=crop&auto=format&q=70' },
    { id:12, categoria:'platos',   nombre:'Pasta Marinara',        precio:65.00,  descripcion:'Salsa de tomate y hierbas',img:'https://images.unsplash.com/photo-1551183053-bf91a1d81141?w=400&fit=crop&auto=format&q=70' },
    /* Entradas */
    { id:13, categoria:'entradas', nombre:'Guacamol con Tostadas', precio:35.00,  descripcion:'Aguacate fresco',          img:'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=400&fit=crop&auto=format&q=70' },
    { id:14, categoria:'entradas', nombre:'Ceviche',               precio:55.00,  descripcion:'Limón y cilantro',         img:'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?w=400&fit=crop&auto=format&q=70' },
    { id:15, categoria:'entradas', nombre:'Alitas de Pollo',       precio:65.00,  descripcion:'BBQ o picante',            img:'https://images.unsplash.com/photo-1608039755401-742074f0548d?w=400&fit=crop&auto=format&q=70' },
    { id:16, categoria:'entradas', nombre:'Sopa del Día',          precio:40.00,  descripcion:'Consultar al mesero',      img:'https://images.unsplash.com/photo-1547592180-85f173990554?w=400&fit=crop&auto=format&q=70' },
    /* Postres */
    { id:17, categoria:'postres',  nombre:'Pastel de Chocolate',   precio:40.00,  descripcion:'Con helado de vainilla',   img:'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400&fit=crop&auto=format&q=70' },
    { id:18, categoria:'postres',  nombre:'Flan de Caramelo',      precio:35.00,  descripcion:'Casero',                   img:'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&fit=crop&auto=format&q=70' },
    { id:19, categoria:'postres',  nombre:'Tres Leches',           precio:38.00,  descripcion:'Con merengue',             img:'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=400&fit=crop&auto=format&q=70' },
    { id:20, categoria:'postres',  nombre:'Helado',                precio:28.00,  descripcion:'3 sabores',                img:'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&fit=crop&auto=format&q=70' },
  ];

  const SEED_COCINA = [
    { id:'PED-001', mesa:'Mesa 3', tipo:'MESA', estado:'EN_PREP', tiempoMin:8, recibidoEn: Date.now() - 8*60000,
      items:[
        { nombre:'Churrasco a la Parrilla', cantidad:2, notas:'Término 3/4' },
        { nombre:'Jugo Natural',            cantidad:2, notas:'Naranja' },
      ]},
    { id:'PED-007', mesa:'Mesa 9', tipo:'MESA', estado:'EN_PREP', tiempoMin:15, recibidoEn: Date.now() - 15*60000,
      items:[
        { nombre:'Mariscos al Ajillo', cantidad:1, notas:'' },
        { nombre:'Caldo de Res',       cantidad:1, notas:'Sin chile' },
        { nombre:'Alitas de Pollo',    cantidad:1, notas:'Picante' },
        { nombre:'Cerveza',            cantidad:4, notas:'Gallo' },
      ]},
    { id:'PED-008', mesa:'Mesa 2', tipo:'MESA', estado:'EN_PREP', tiempoMin:22, recibidoEn: Date.now() - 22*60000,
      items:[
        { nombre:'Pasta Marinara', cantidad:2, notas:'' },
        { nombre:'Limonada',       cantidad:2, notas:'Sin soda' },
      ]},
  ];

  const SEED_EMPRESAS = [
    { id:1, nombre:'Ferrocarriles de Guatemala S.A.', nit:'1234567-8', limiteCredito:5000.00, utilizado:0,
      contacto:'Laura Méndez', telefono:'2333-4444' },
    { id:2, nombre:'Puerto Barrios Logística',        nit:'9876543-2', limiteCredito:3000.00, utilizado:0,
      contacto:'Mario Salazar', telefono:'7948-0011' },
    { id:3, nombre:'Importadora Izabal',              nit:'5555444-1', limiteCredito:2000.00, utilizado:0,
      contacto:'Sofía Ruiz',    telefono:'7948-7878' },
  ];

  const SEED_FACTURAS = [];   // Se llenan al emitir
  const SEED_ABONOS = [];     // Se llenan al pagar

  const SEED_PROVEEDORES = [
    { id:1, nombre:'Distribuidora GEMSA',    contacto:'Luis García',     telefono:'7948-1234', email:'ventas@gemsa.gt'         },
    { id:2, nombre:'Carnes Premium S.A.',    contacto:'Rosa Hernández',  telefono:'7948-5678', email:'pedidos@carnespremium.gt'},
    { id:3, nombre:'Mariscos del Caribe',    contacto:'Mario Bosch',     telefono:'7948-9012', email:'mario@mariscosc.gt'      },
    { id:4, nombre:'Bebidas y Más',          contacto:'Sandra Tun',      telefono:'7948-3456', email:'cotizaciones@bebidas.gt' },
  ];

  const SEED_CUENTAS_PROV = [
    { id:1, proveedorId:1, concepto:'Abarrotes semana 18', monto:2400.00, fechaVence:'2026-05-05', estado:'VENCIDA' },
    { id:2, proveedorId:2, concepto:'Carnes 06/05',        monto:1850.00, fechaVence:'2026-05-15', estado:'VIGENTE' },
    { id:3, proveedorId:3, concepto:'Mariscos 07/05',      monto:950.00,  fechaVence:'2026-05-12', estado:'VIGENTE' },
    { id:4, proveedorId:4, concepto:'Bebidas semana 17',   monto:1200.00, fechaVence:'2026-04-28', estado:'VENCIDA' },
    { id:5, proveedorId:1, concepto:'Abarrotes semana 19', monto:2100.00, fechaVence:'2026-05-10', estado:'VIGENTE' },
    { id:6, proveedorId:4, concepto:'Bebidas semana 18',   monto:1350.00, fechaVence:'2026-04-20', estado:'PAGADA'  },
    { id:7, proveedorId:1, concepto:'Abarrotes semana 17', monto:2200.00, fechaVence:'2026-04-28', estado:'PAGADA'  },
  ];

  const SEED_CATEGORIAS = [
    { key:'entradas', label:'Entradas',   icon:'bi-egg-fried',   bg:'#E8F5E9', fg:'#2E7D32' },
    { key:'platos',   label:'Platos',     icon:'bi-fire',        bg:'#FFF3E0', fg:'#E65100' },
    { key:'postres',  label:'Postres',    icon:'bi-cake2',       bg:'#FCE4EC', fg:'#C2185B' },
    { key:'bebidas',  label:'Bebidas',    icon:'bi-cup-straw',   bg:'#E3F2FD', fg:'#1565C0' },
  ];

  const SEED_EMPLEADOS = [
    { id:1, nombre:'Carlos Méndez', puesto:'ADMIN',  salarioBruto:6000.00, igss:289.80, isr:120.00 },
    { id:2, nombre:'Ana García',    puesto:'MESERO', salarioBruto:3500.00, igss:169.05, isr:0 },
    { id:3, nombre:'José López',    puesto:'COCINA', salarioBruto:3800.00, igss:183.54, isr:0 },
    { id:4, nombre:'María Pérez',   puesto:'CAJERO', salarioBruto:4000.00, igss:193.20, isr:0 },
    { id:5, nombre:'Pedro Caal',    puesto:'MESERO', salarioBruto:3500.00, igss:169.05, isr:0 },
    { id:6, nombre:'Luisa Choc',    puesto:'COCINA', salarioBruto:3600.00, igss:173.88, isr:0 },
    { id:7, nombre:'Roberto Quej',  puesto:'COCINA', salarioBruto:3200.00, igss:154.56, isr:0 },
  ];

  const SEED_CONFIG = {
    restaurante: {
      nombre: 'Restaurante La Antigua',
      direccion: 'Puerto Barrios, Izabal · Guatemala',
      telefono: '+502 7948-0000',
      nit: '12345678-9',
      eslogan: 'Sabores auténticos del Caribe guatemalteco',
    },
    impuestos: {
      iva: 12,
      moneda: 'Q',
    },
    fel: {
      online: true,
      establecimiento: 'Sucursal Principal',
    },
    notificaciones: {
      sonidoCocina: true,
      alertasVencimiento: true,
    },
  };

  /* ── Helpers ── */
  function load(key, seed) {
    try {
      const raw = localStorage.getItem(key);
      if (raw) return JSON.parse(raw);
    } catch {}
    if (seed !== undefined) {
      localStorage.setItem(key, JSON.stringify(seed));
      return JSON.parse(JSON.stringify(seed));
    }
    return null;
  }

  function save(key, value) {
    try { localStorage.setItem(key, JSON.stringify(value)); } catch {}
  }

  /* ── API pública ── */
  const API = {
    KEYS,

    /* ── Mesas ── */
    getMesas() {
  if (window.SIGRA_MESAS_SEED) return window.SIGRA_MESAS_SEED;
  return load(KEYS.MESAS, SEED_MESAS);
},
    setMesas(arr) { save(KEYS.MESAS, arr); },
    updateMesa(id, patch) {
      const arr = API.getMesas().map(m => m.id === id ? { ...m, ...patch } : m);
      save(KEYS.MESAS, arr);
      return arr;
    },

    /* ── Menú ── */
    getMenu() {
  if (window.SIGRA_MENU_SEED) return window.SIGRA_MENU_SEED;
  return load(KEYS.MENU, SEED_MENU);
},
    setMenu(arr) { save(KEYS.MENU, arr); },
    updateMenuItem(id, patch) {
      const arr = API.getMenu().map(p => p.id === id ? { ...p, ...patch } : p);
      save(KEYS.MENU, arr);
      return arr;
    },
    addMenuItem(item) {
      const arr = API.getMenu();
      const id = Math.max(0, ...arr.map(x => x.id)) + 1;
      arr.push({ ...item, id });
      save(KEYS.MENU, arr);
      return id;
    },
    deleteMenuItem(id) {
      const arr = API.getMenu().filter(p => p.id !== id);
      save(KEYS.MENU, arr);
      return arr;
    },
    resetMenu() { save(KEYS.MENU, SEED_MENU); return API.getMenu(); },

    /* ── Categorías ── */
    getCategorias() { return load(KEYS.CATEGORIAS, SEED_CATEGORIAS); },
    setCategorias(arr) { save(KEYS.CATEGORIAS, arr); },
    addCategoria(cat) {
      const arr = API.getCategorias();
      if (arr.some(c => c.key === cat.key)) return false;
      arr.push(cat);
      save(KEYS.CATEGORIAS, arr);
      return true;
    },
    deleteCategoria(key) {
      const arr = API.getCategorias().filter(c => c.key !== key);
      save(KEYS.CATEGORIAS, arr);
      return arr;
    },

    /* ── Empleados ── */
    getEmpleados() {
  if (window.SIGRA_EMPLEADOS_SEED) return window.SIGRA_EMPLEADOS_SEED;
  return load(KEYS.EMPLEADOS, SEED_EMPLEADOS);
},
    setEmpleados(arr) { save(KEYS.EMPLEADOS, arr); },
    addEmpleado(emp) {
      const arr = API.getEmpleados();
      const id = Math.max(0, ...arr.map(x => x.id)) + 1;
      arr.push({ ...emp, id });
      save(KEYS.EMPLEADOS, arr);
      return id;
    },
    deleteEmpleado(id) {
      const arr = API.getEmpleados().filter(e => e.id !== id);
      save(KEYS.EMPLEADOS, arr);
      return arr;
    },

    /* ── Mesas CRUD admin ── */
    addMesa(mesa) {
      const arr = API.getMesas();
      const id = Math.max(0, ...arr.map(x => x.id)) + 1;
      const numero = mesa.numero || (Math.max(0, ...arr.map(x => x.numero)) + 1);
      arr.push({ id, numero, capacidad: mesa.capacidad || 4, estado: 'LIBRE', pedido: null, reserva: null });
      save(KEYS.MESAS, arr);
      return id;
    },
    deleteMesa(id) {
      const arr = API.getMesas().filter(m => m.id !== id);
      save(KEYS.MESAS, arr);
      return arr;
    },

    /* ── Cocina ── */
    getCocina() {
  if (window.SIGRA_COCINA_SEED) return window.SIGRA_COCINA_SEED;
  return load(KEYS.COCINA, SEED_COCINA);
},
    setCocina(arr) { save(KEYS.COCINA, arr); },
    addCocinaPedido(p) {
      const arr = API.getCocina();
      arr.unshift({ ...p, recibidoEn: Date.now() });
      save(KEYS.COCINA, arr);
    },

    /* ── Pedidos secuenciales ── */
    nextPedidoId() {
      const n = (load(KEYS.PEDIDO_SEQ, 100) || 100) + 1;
      save(KEYS.PEDIDO_SEQ, n);
      return `PED-${String(n).padStart(3, '0')}`;
    },

    /* ── Facturas ── */
    getFacturas() { return load(KEYS.FACTURAS, SEED_FACTURAS); },
    addFactura(f) {
      const arr = API.getFacturas();
      arr.unshift(f);
      save(KEYS.FACTURAS, arr);
      return f;
    },

    /* ── Empresas (crédito) ── */
    getEmpresas() {
  if (window.SIGRA_EMPRESAS_SEED) return window.SIGRA_EMPRESAS_SEED;
  return load(KEYS.EMPRESAS, SEED_EMPRESAS);
},
    setEmpresas(arr) { save(KEYS.EMPRESAS, arr); },
    addEmpresa(emp) {
      const arr = API.getEmpresas();
      arr.push({ ...emp, id: Date.now() });
      save(KEYS.EMPRESAS, arr);
      return arr;
    },
    updateEmpresa(id, patch) {
      const arr = API.getEmpresas().map(e => e.id === id ? { ...e, ...patch } : e);
      save(KEYS.EMPRESAS, arr);
      return arr;
    },
    cargarCredito(empresaId, monto, refFactura) {
      const emp = API.getEmpresas().find(e => e.id === empresaId);
      if (!emp) return { ok:false, reason:'Empresa no encontrada' };
      const nuevoUtilizado = emp.utilizado + monto;
      if (nuevoUtilizado > emp.limiteCredito) {
        return { ok:false, reason:'overlimit', disponible: emp.limiteCredito - emp.utilizado };
      }
      API.updateEmpresa(empresaId, { utilizado: nuevoUtilizado });
      const abonos = API.getAbonos();
      abonos.unshift({
        id: Date.now(),
        empresaId, monto: monto, tipo: 'CARGO',
        fecha: new Date().toISOString().slice(0,10),
        ref: refFactura || '',
      });
      save(KEYS.ABONOS, abonos);
      return { ok:true };
    },

    /* ── Abonos (movimientos de crédito) ── */
    getAbonos() { return load(KEYS.ABONOS, SEED_ABONOS); },
    setAbonos(arr) { save(KEYS.ABONOS, arr); },
    registrarAbono(empresaId, monto, ref) {
      const emp = API.getEmpresas().find(e => e.id === empresaId);
      if (!emp) return { ok:false };
      const aplicado = Math.min(monto, emp.utilizado);
      const sobrante = monto - aplicado;
      API.updateEmpresa(empresaId, { utilizado: Math.max(0, emp.utilizado - monto) });
      const abonos = API.getAbonos();
      abonos.unshift({
        id: Date.now(),
        empresaId, monto: aplicado, tipo: 'ABONO',
        fecha: new Date().toISOString().slice(0,10),
        ref: ref || '',
      });
      save(KEYS.ABONOS, abonos);
      return { ok:true, aplicado, sobrante, liquidado: (emp.utilizado - aplicado) <= 0 };
    },

    /* ── Proveedores ── */
    getProveedores() {
  if (window.SIGRA_PROVEEDORES_SEED) return window.SIGRA_PROVEEDORES_SEED;
  return load(KEYS.PROVEEDORES, SEED_PROVEEDORES);
},
    setProveedores(arr) { save(KEYS.PROVEEDORES, arr); },
    addProveedor(p) {
      const arr = API.getProveedores();
      const id = Math.max(0, ...arr.map(x => x.id)) + 1;
      arr.push({ ...p, id });
      save(KEYS.PROVEEDORES, arr);
      return id;
    },

    getCuentasProv() {
  if (window.SIGRA_CUENTAS_PROV_SEED) return window.SIGRA_CUENTAS_PROV_SEED;
  return load(KEYS.CUENTAS_PROV, SEED_CUENTAS_PROV);
},
    setCuentasProv(arr) { save(KEYS.CUENTAS_PROV, arr); },
    addCuentaProv(c) {
      const arr = API.getCuentasProv();
      const id = Math.max(0, ...arr.map(x => x.id)) + 1;
      arr.push({ ...c, id });
      save(KEYS.CUENTAS_PROV, arr);
      return id;
    },
    updateCuentaProv(id, patch) {
      const arr = API.getCuentasProv().map(c => c.id === id ? { ...c, ...patch } : c);
      save(KEYS.CUENTAS_PROV, arr);
    },

    /* ── Planillas ── */
    getPlanillas() { return load(KEYS.PLANILLAS, []); },
    addPlanilla(p) {
      const arr = API.getPlanillas();
      arr.unshift(p);
      save(KEYS.PLANILLAS, arr);
    },

    /* ── Config ── */
    getConfig() { return load(KEYS.CONFIG, SEED_CONFIG); },
    setConfig(cfg) { save(KEYS.CONFIG, cfg); },

    /* ── Asistencia ── */
    getAsistencia() { return load(KEYS.ASISTENCIA, []); },
    setAsistencia(arr) { save(KEYS.ASISTENCIA, arr); },
    todayKey() { return new Date().toISOString().slice(0, 10); },
    nowTime() {
      const d = new Date();
      return d.toTimeString().slice(0, 5);
    },
    getAsistenciaHoy(usuario) {
      const fecha = API.todayKey();
      return API.getAsistencia().find(a => a.usuario === usuario && a.fecha === fecha) || null;
    },
    marcarAsistencia(usuario, nombre, evento) {
      const arr = API.getAsistencia();
      const fecha = API.todayKey();
      const hora = API.nowTime();
      let reg = arr.find(a => a.usuario === usuario && a.fecha === fecha);
      if (!reg) {
        reg = { id: Date.now(), usuario, nombre, fecha, entrada: null, salida: null, almuerzoIni: null, almuerzoFin: null };
        arr.unshift(reg);
      }
      if (evento === 'ENTRADA' && !reg.entrada) reg.entrada = hora;
      else if (evento === 'SALIDA') reg.salida = hora;
      else if (evento === 'ALMUERZO_INI' && !reg.almuerzoIni) reg.almuerzoIni = hora;
      else if (evento === 'ALMUERZO_FIN' && !reg.almuerzoFin) reg.almuerzoFin = hora;
      else return { ok: false, reason: 'evento ya registrado o inválido' };
      save(KEYS.ASISTENCIA, arr);
      return { ok: true, registro: reg };
    },

    /* ── Permisos ── */
    getPermisos() { return load(KEYS.PERMISOS, []); },
    addPermiso(p) {
      const arr = API.getPermisos();
      arr.unshift({ id: Date.now(), estado: 'PENDIENTE', creado: new Date().toISOString().slice(0,10), ...p });
      save(KEYS.PERMISOS, arr);
    },
    updatePermiso(id, patch) {
      const arr = API.getPermisos().map(p => p.id === id ? { ...p, ...patch } : p);
      save(KEYS.PERMISOS, arr);
    },

    /* ── Reset total ── */
    resetAll() {
      Object.values(KEYS).forEach(k => localStorage.removeItem(k));
    },
  };

  window.SIGRA_DATA = API;
})();
