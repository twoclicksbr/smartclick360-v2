<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmoduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $slug, string $module, string $submodule)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module, string $submodule)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module, string $module_id, string $submodule)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Decodifica o code para register_id
        $registerId = decodeId($module_id);

        // Mapeamento de submódulos para models
        $submoduleModelMap = [
            'contacts' => \App\Models\Tenant\Contact::class,
            'documents' => \App\Models\Tenant\Document::class,
            'addresses' => \App\Models\Tenant\Address::class,
            'files' => \App\Models\Tenant\File::class,
            'notes' => \App\Models\Tenant\Note::class,
        ];

        // Verifica se o submódulo existe
        if (!isset($submoduleModelMap[$submodule])) {
            abort(404, 'Submódulo não encontrado');
        }

        $modelClass = $submoduleModelMap[$submodule];

        // Busca o module_id pelo slug
        $moduleRecord = \App\Models\Tenant\Module::where('slug', $module)->firstOrFail();

        // Validação específica por submódulo
        if ($submodule === 'contacts') {
            // Validação básica primeiro
            $basicValidation = $request->validate([
                'type_contact_id' => 'required|exists:type_contacts,id',
            ]);

            // Busca o tipo de contato para verificar se é Email
            $typeContact = \App\Models\Tenant\TypeContact::find($request->type_contact_id);

            // Define regras de validação para o valor
            $rules = [
                'value' => 'required|string|max:255',
            ];

            // Se for Email, adiciona validação de unicidade e formato
            if ($typeContact && $typeContact->name === 'Email') {
                // Verifica se já existe um email igual para esta pessoa
                $existingCount = \App\Models\Tenant\Contact::where('value', $request->value)
                    ->where('type_contact_id', $typeContact->id)
                    ->where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->count();

                if ($existingCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este email já está cadastrado.',
                        'errors' => [
                            'value' => ['Este email já está cadastrado.']
                        ]
                    ], 422);
                }

                $rules['value'] = [
                    'required',
                    'string',
                    'max:255',
                    'email',
                ];
            }

            $validated = $request->validate($rules, [
                'value.email' => 'Digite um email válido.',
            ]);

            // Adiciona o type_contact_id no validated
            $validated['type_contact_id'] = $basicValidation['type_contact_id'];

            // Remove máscara (deixa só números para telefones, mantém @ para email)
            $validated['value'] = preg_replace('/[^0-9a-zA-Z@.\-_]/', '', $validated['value']);

            $data = [
                'module_id' => $moduleRecord->id,
                'register_id' => $registerId,
                'type_contact_id' => $validated['type_contact_id'],
                'value' => $validated['value'],
                'order' => $modelClass::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->max('order') + 1,
                'status' => true,
            ];
        } elseif ($submodule === 'documents') {
            // Validação básica
            $validated = $request->validate([
                'type_document_id' => 'required|exists:type_documents,id',
                'value' => 'required|string|max:255',
                'expiration_date' => 'nullable|date',
            ]);

            // Remove máscara (deixa só números e letras)
            $validated['value'] = preg_replace('/[^0-9a-zA-Z]/', '', $validated['value']);

            $data = [
                'module_id' => $moduleRecord->id,
                'register_id' => $registerId,
                'type_document_id' => $validated['type_document_id'],
                'value' => $validated['value'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'order' => $modelClass::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->max('order') + 1,
                'status' => true,
            ];
        } elseif ($submodule === 'addresses') {
            // Validação
            $validated = $request->validate([
                'type_address_id' => 'required|exists:type_addresses,id',
                'zip_code' => 'required|string|max:10',
                'street' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'complement' => 'nullable|string|max:100',
                'neighborhood' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:2',
                'country' => 'required|string|max:2',
            ]);

            // Remove máscara do CEP (deixa só números)
            $validated['zip_code'] = preg_replace('/[^0-9]/', '', $validated['zip_code']);

            // Se está marcando como principal, desmarca todos os outros
            if ($request->has('is_main')) {
                \App\Models\Tenant\Address::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->update(['is_main' => false]);
            }

            // Se é o primeiro endereço, marca como principal automaticamente
            $isFirstAddress = \App\Models\Tenant\Address::where('module_id', $moduleRecord->id)
                ->where('register_id', $registerId)
                ->count() === 0;

            $data = [
                'module_id' => $moduleRecord->id,
                'register_id' => $registerId,
                'type_address_id' => $validated['type_address_id'],
                'zip_code' => $validated['zip_code'],
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'is_main' => $isFirstAddress ? true : $request->has('is_main'),
                'order' => $modelClass::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->max('order') + 1,
                'status' => true,
            ];
        } elseif ($submodule === 'notes') {
            // Validação
            $validated = $request->validate([
                'content' => 'required|string|max:5000',
            ]);

            $data = [
                'module_id' => $moduleRecord->id,
                'register_id' => $registerId,
                'title' => null,
                'content' => $validated['content'],
                'order' => $modelClass::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->max('order') + 1,
                'status' => $request->input('status', 1) == 1,
            ];
        } elseif ($submodule === 'files') {
            // Validação
            $validated = $request->validate([
                'file' => 'required|file|max:10240', // max 10MB
            ]);

            // Upload do arquivo
            $uploadedFile = $request->file('file');
            $tenant = request()->attributes->get('tenant');

            // Define o caminho de armazenamento
            $path = $uploadedFile->store('tenants/' . $tenant->slug . '/files', 'public');

            $data = [
                'module_id' => $moduleRecord->id,
                'register_id' => $registerId,
                'name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $uploadedFile->getClientMimeType(),
                'size' => $uploadedFile->getSize(),
                'order' => $modelClass::where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->max('order') + 1,
                'status' => $request->input('status', 1) == 1,
            ];
        } else {
            abort(404, 'Submódulo não implementado');
        }

        // Cria o registro
        $record = $modelClass::create($data);

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            if ($submodule === 'contacts') {
                // Carrega o relacionamento type_contact
                $record->load('typeContact');

                return response()->json([
                    'success' => true,
                    'message' => ucfirst($submodule) . ' adicionado com sucesso!',
                    'contact' => [
                        'id' => $record->id,
                        'type_contact' => [
                            'id' => $record->typeContact->id,
                            'name' => $record->typeContact->name,
                            'mask' => $record->typeContact->mask,
                        ],
                        'type_name' => $record->typeContact->name,
                        'type_mask' => $record->typeContact->mask,
                        'value' => $record->value,
                    ],
                ]);
            } elseif ($submodule === 'documents') {
                // Carrega o relacionamento type_document
                $record->load('typeDocument');

                return response()->json([
                    'success' => true,
                    'message' => 'Documento adicionado com sucesso!',
                    'document' => [
                        'id' => $record->id,
                        'type_document' => [
                            'id' => $record->typeDocument->id,
                            'name' => $record->typeDocument->name,
                            'mask' => $record->typeDocument->mask,
                        ],
                        'type_name' => $record->typeDocument->name,
                        'type_mask' => $record->typeDocument->mask,
                        'value' => $record->value,
                        'expiration_date' => $record->expiration_date ? $record->expiration_date->format('Y-m-d') : null,
                        'expiration_date_formatted' => $record->expiration_date ? $record->expiration_date->format('d/m/Y') : null,
                    ],
                ]);
            } elseif ($submodule === 'addresses') {
                // Carrega o relacionamento type_address
                $record->load('typeAddress');

                return response()->json([
                    'success' => true,
                    'message' => 'Endereço adicionado com sucesso!',
                    'address' => [
                        'id' => $record->id,
                        'type_address' => [
                            'id' => $record->typeAddress->id,
                            'name' => $record->typeAddress->name,
                        ],
                        'type_name' => $record->typeAddress->name,
                        'zip_code' => $record->zip_code,
                        'street' => $record->street,
                        'number' => $record->number,
                        'complement' => $record->complement,
                        'neighborhood' => $record->neighborhood,
                        'city' => $record->city,
                        'state' => $record->state,
                        'country' => $record->country,
                        'is_main' => $record->is_main,
                    ],
                ]);
            } elseif ($submodule === 'notes') {
                return response()->json([
                    'success' => true,
                    'message' => 'Observação adicionada com sucesso!',
                    'note' => [
                        'id' => $record->id,
                        'content' => $record->content,
                        'created_at' => $record->created_at,
                        'status' => $record->status,
                    ],
                ]);
            } elseif ($submodule === 'files') {
                return response()->json([
                    'success' => true,
                    'message' => 'Arquivo enviado com sucesso!',
                    'file' => [
                        'id' => $record->id,
                        'name' => $record->name,
                        'path' => $record->path,
                        'mime_type' => $record->mime_type,
                        'size' => $record->size,
                        'created_at' => $record->created_at->format('d/m/Y H:i'),
                        'status' => $record->status,
                    ],
                ]);
            }
        }

        return redirect()->back()->with('success', ucfirst($submodule) . ' adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, string $module, string $m_id, string $submodule, string $s_id)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Mapeamento de submódulos para models
        $submoduleModelMap = [
            'contacts' => \App\Models\Tenant\Contact::class,
            'documents' => \App\Models\Tenant\Document::class,
            'addresses' => \App\Models\Tenant\Address::class,
            'files' => \App\Models\Tenant\File::class,
            'notes' => \App\Models\Tenant\Note::class,
        ];

        // Verifica se o submódulo existe
        if (!isset($submoduleModelMap[$submodule])) {
            abort(404, 'Submódulo não encontrado');
        }

        $modelClass = $submoduleModelMap[$submodule];

        // Busca o registro
        $record = $modelClass::findOrFail($s_id);

        // Se for requisição AJAX, retorna JSON
        if (request()->ajax()) {
            if ($submodule === 'contacts') {
                $record->load('typeContact');
                return response()->json([
                    'success' => true,
                    'contact' => [
                        'id' => $record->id,
                        'type_contact_id' => $record->type_contact_id,
                        'type_name' => $record->typeContact->name,
                        'value' => $record->value,
                        'status' => $record->status,
                    ],
                ]);
            } elseif ($submodule === 'documents') {
                $record->load('typeDocument');
                return response()->json([
                    'success' => true,
                    'document' => [
                        'id' => $record->id,
                        'type_document_id' => $record->type_document_id,
                        'type_name' => $record->typeDocument->name,
                        'value' => $record->value,
                        'expiration_date' => $record->expiration_date ? $record->expiration_date->format('Y-m-d') : null,
                        'status' => $record->status,
                    ],
                ]);
            } elseif ($submodule === 'addresses') {
                $record->load('typeAddress');
                return response()->json([
                    'success' => true,
                    'address' => [
                        'id' => $record->id,
                        'type_address_id' => $record->type_address_id,
                        'type_name' => $record->typeAddress->name,
                        'zip_code' => $record->zip_code,
                        'street' => $record->street,
                        'number' => $record->number,
                        'complement' => $record->complement,
                        'neighborhood' => $record->neighborhood,
                        'city' => $record->city,
                        'state' => $record->state,
                        'country' => $record->country,
                        'is_main' => $record->is_main,
                        'status' => $record->status,
                    ],
                ]);
            } elseif ($submodule === 'notes') {
                return response()->json([
                    'success' => true,
                    'note' => [
                        'id' => $record->id,
                        'content' => $record->content,
                        'status' => $record->status,
                    ],
                ]);
            }
        }

        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $submodule, string $id)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $m_id, string $submodule, string $s_id)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Mapeamento de submódulos para models
        $submoduleModelMap = [
            'contacts' => \App\Models\Tenant\Contact::class,
            'documents' => \App\Models\Tenant\Document::class,
            'addresses' => \App\Models\Tenant\Address::class,
            'files' => \App\Models\Tenant\File::class,
            'notes' => \App\Models\Tenant\Note::class,
        ];

        // Verifica se o submódulo existe
        if (!isset($submoduleModelMap[$submodule])) {
            abort(404, 'Submódulo não encontrado');
        }

        $modelClass = $submoduleModelMap[$submodule];

        // Busca o module_id pelo slug
        $moduleRecord = \App\Models\Tenant\Module::where('slug', $module)->firstOrFail();

        // Busca o registro
        $record = $modelClass::findOrFail($s_id);

        // Validação específica por submódulo
        if ($submodule === 'contacts') {
            // Validação básica primeiro
            $basicValidation = $request->validate([
                'type_contact_id' => 'required|exists:type_contacts,id',
            ]);

            // Busca o tipo de contato para verificar se é Email
            $typeContact = \App\Models\Tenant\TypeContact::find($request->type_contact_id);

            // Define regras de validação para o valor
            $rules = [
                'value' => 'required|string|max:255',
            ];

            // Se for Email, adiciona validação de unicidade e formato (exceto o próprio registro)
            if ($typeContact && $typeContact->name === 'Email') {
                // Verifica se já existe um email igual para esta pessoa (exceto o próprio registro)
                $existingCount = \App\Models\Tenant\Contact::where('value', $request->value)
                    ->where('type_contact_id', $typeContact->id)
                    ->where('module_id', $record->module_id)
                    ->where('register_id', $record->register_id)
                    ->where('id', '!=', $s_id)
                    ->count();

                if ($existingCount > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este email já está cadastrado.',
                        'errors' => [
                            'value' => ['Este email já está cadastrado.']
                        ]
                    ], 422);
                }

                $rules['value'] = [
                    'required',
                    'string',
                    'max:255',
                    'email',
                ];
            }

            $validated = $request->validate($rules, [
                'value.email' => 'Digite um email válido.',
            ]);

            // Adiciona o type_contact_id no validated
            $validated['type_contact_id'] = $basicValidation['type_contact_id'];

            // Remove máscara (deixa só números para telefones, mantém @ para email)
            $validated['value'] = preg_replace('/[^0-9a-zA-Z@.\-_]/', '', $validated['value']);

            // Atualiza o registro
            $record->update([
                'type_contact_id' => $validated['type_contact_id'],
                'value' => $validated['value'],
                'status' => $request->has('status') ? $request->status : true,
            ]);
        } elseif ($submodule === 'documents') {
            // Validação básica
            $validated = $request->validate([
                'type_document_id' => 'required|exists:type_documents,id',
                'value' => 'required|string|max:255',
                'expiration_date' => 'nullable|date',
            ]);

            // Remove máscara
            $validated['value'] = preg_replace('/[^0-9a-zA-Z]/', '', $validated['value']);

            // Atualiza o registro
            $record->update([
                'type_document_id' => $validated['type_document_id'],
                'value' => $validated['value'],
                'expiration_date' => $validated['expiration_date'] ?? null,
                'status' => $request->has('status') ? $request->status : true,
            ]);
        } elseif ($submodule === 'addresses') {
            // Validação básica
            $validated = $request->validate([
                'type_address_id' => 'required|exists:type_addresses,id',
                'zip_code' => 'required|string|max:10',
                'street' => 'required|string|max:255',
                'number' => 'required|string|max:20',
                'complement' => 'nullable|string|max:100',
                'neighborhood' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:2',
                'country' => 'required|string|max:2',
            ]);

            // Remove máscara do CEP
            $validated['zip_code'] = preg_replace('/[^0-9]/', '', $validated['zip_code']);

            // Se está marcando como principal, desmarca todos os outros (exceto o atual)
            if ($request->has('is_main')) {
                \App\Models\Tenant\Address::where('module_id', $record->module_id)
                    ->where('register_id', $record->register_id)
                    ->where('id', '!=', $record->id)
                    ->update(['is_main' => false]);
            }

            // Atualiza o registro
            $record->update([
                'type_address_id' => $validated['type_address_id'],
                'zip_code' => $validated['zip_code'],
                'street' => $validated['street'],
                'number' => $validated['number'],
                'complement' => $validated['complement'] ?? null,
                'neighborhood' => $validated['neighborhood'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'is_main' => $request->has('is_main'),
                'status' => $request->has('status') ? $request->status : true,
            ]);
        } elseif ($submodule === 'notes') {
            // Validação
            $validated = $request->validate([
                'content' => 'required|string|max:5000',
            ]);

            // Atualiza o registro
            $record->update([
                'title' => null,
                'content' => $validated['content'],
                'status' => $request->input('status', 1) == 1,
            ]);
        } else {
            abort(404, 'Submódulo não implementado');
        }

        // Se for requisição AJAX, retorna JSON
        if ($request->ajax()) {
            if ($submodule === 'contacts') {
                // Recarrega o relacionamento type_contact
                $record->load('typeContact');

                return response()->json([
                    'success' => true,
                    'message' => ucfirst($submodule) . ' atualizado com sucesso!',
                    'contact' => [
                        'id' => $record->id,
                        'type_contact' => [
                            'id' => $record->typeContact->id,
                            'name' => $record->typeContact->name,
                            'mask' => $record->typeContact->mask,
                        ],
                        'type_name' => $record->typeContact->name,
                        'type_mask' => $record->typeContact->mask,
                        'value' => $record->value,
                    ],
                ]);
            } elseif ($submodule === 'documents') {
                // Recarrega o relacionamento type_document
                $record->load('typeDocument');

                return response()->json([
                    'success' => true,
                    'message' => 'Documento atualizado com sucesso!',
                    'document' => [
                        'id' => $record->id,
                        'type_document' => [
                            'id' => $record->typeDocument->id,
                            'name' => $record->typeDocument->name,
                            'mask' => $record->typeDocument->mask,
                        ],
                        'type_name' => $record->typeDocument->name,
                        'type_mask' => $record->typeDocument->mask,
                        'value' => $record->value,
                        'expiration_date' => $record->expiration_date ? $record->expiration_date->format('Y-m-d') : null,
                        'expiration_date_formatted' => $record->expiration_date ? $record->expiration_date->format('d/m/Y') : null,
                    ],
                ]);
            } elseif ($submodule === 'addresses') {
                // Recarrega o relacionamento type_address
                $record->load('typeAddress');

                return response()->json([
                    'success' => true,
                    'message' => 'Endereço atualizado com sucesso!',
                    'address' => [
                        'id' => $record->id,
                        'type_address' => [
                            'id' => $record->typeAddress->id,
                            'name' => $record->typeAddress->name,
                        ],
                        'type_name' => $record->typeAddress->name,
                        'zip_code' => $record->zip_code,
                        'street' => $record->street,
                        'number' => $record->number,
                        'complement' => $record->complement,
                        'neighborhood' => $record->neighborhood,
                        'city' => $record->city,
                        'state' => $record->state,
                        'country' => $record->country,
                        'is_main' => $record->is_main,
                    ],
                ]);
            } elseif ($submodule === 'notes') {
                return response()->json([
                    'success' => true,
                    'message' => 'Observação atualizada com sucesso!',
                    'note' => [
                        'id' => $record->id,
                        'content' => $record->content,
                        'created_at' => $record->created_at,
                        'status' => $record->status,
                    ],
                ]);
            }
        }

        return redirect()->back()->with('success', ucfirst($submodule) . ' atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug, string $module, string $m_id, string $submodule, string $s_id)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Mapeamento de submódulos para models
        $submoduleModelMap = [
            'contacts' => \App\Models\Tenant\Contact::class,
            'documents' => \App\Models\Tenant\Document::class,
            'addresses' => \App\Models\Tenant\Address::class,
            'files' => \App\Models\Tenant\File::class,
            'notes' => \App\Models\Tenant\Note::class,
        ];

        // Verifica se o submódulo existe
        if (!isset($submoduleModelMap[$submodule])) {
            abort(404, 'Submódulo não encontrado');
        }

        $modelClass = $submoduleModelMap[$submodule];

        // Busca o registro
        $record = $modelClass::findOrFail($s_id);

        // Faz soft delete
        $record->delete();

        // Se for requisição AJAX, retorna JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($submodule) . ' excluído com sucesso!',
            ]);
        }

        return redirect()->back()->with('success', ucfirst($submodule) . ' excluído com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $m_id, string $submodule, string $s_id)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        abort(404);
    }

    /**
     * Reorder resources (drag and drop)
     */
    public function reorder(Request $request, string $slug, string $module, string $m_id, string $submodule)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Mapeamento de submódulos para models
        $submoduleModelMap = [
            'contacts' => \App\Models\Tenant\Contact::class,
            'documents' => \App\Models\Tenant\Document::class,
            'addresses' => \App\Models\Tenant\Address::class,
            'files' => \App\Models\Tenant\File::class,
            'notes' => \App\Models\Tenant\Note::class,
        ];

        // Verifica se o submódulo existe no mapeamento
        if (!isset($submoduleModelMap[$submodule])) {
            return response()->json(['success' => false, 'message' => 'Submódulo não suporta reordenação'], 404);
        }

        $modelClass = $submoduleModelMap[$submodule];
        $order = $request->input('order', []);

        foreach ($order as $item) {
            $modelClass::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
