<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Tenant\Address;
use App\Models\Tenant\Contact;
use App\Models\Tenant\Document;
use App\Models\Tenant\File;
use App\Models\Tenant\Module;
use App\Models\Tenant\Note;
use App\Models\Tenant\TypeContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmoduleController extends Controller
{
    use ApiResponse;

    /**
     * Mapeia submodule slug → Model class + relacionamentos
     */
    private function resolveSubmodule(string $submodule): ?array
    {
        $map = [
            'contacts' => [
                'model' => Contact::class,
                'with' => ['typeContact'],
                'module_slug' => 'contacts',
            ],
            'documents' => [
                'model' => Document::class,
                'with' => ['typeDocument'],
                'module_slug' => 'documents',
            ],
            'addresses' => [
                'model' => Address::class,
                'with' => ['typeAddress'],
                'module_slug' => 'addresses',
            ],
            'files' => [
                'model' => File::class,
                'with' => [],
                'module_slug' => 'files',
            ],
            'notes' => [
                'model' => Note::class,
                'with' => [],
                'module_slug' => 'notes',
            ],
        ];

        return $map[$submodule] ?? null;
    }

    public function index(Request $request, string $module, string $code, string $submodule): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $registerId = decodeId($code);
        $moduleId = Module::where('slug', $module)->value('id');

        if (!$moduleId) {
            return $this->notFound("Módulo '{$module}' não encontrado");
        }

        $items = $config['model']::with($config['with'])
            ->where('module_id', $moduleId)
            ->where('register_id', $registerId)
            ->orderBy('order', 'asc')
            ->get();

        return $this->success([
            $submodule => $items,
        ]);
    }

    public function store(Request $request, string $module, string $code, string $submodule): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $registerId = decodeId($code);
        $moduleRecord = Module::where('slug', $module)->firstOrFail();
        $modelClass = $config['model'];

        // Validação e criação específica por submódulo
        if ($submodule === 'contacts') {
            // Validação básica primeiro
            $basicValidation = $request->validate([
                'type_contact_id' => 'required|exists:type_contacts,id',
            ]);

            // Busca o tipo de contato para verificar se é Email
            $typeContact = TypeContact::find($request->type_contact_id);

            // Define regras de validação para o valor
            $rules = [
                'value' => 'required|string|max:255',
            ];

            // Se for Email, adiciona validação de unicidade e formato
            if ($typeContact && $typeContact->name === 'Email') {
                // Verifica se já existe um email igual para esta pessoa
                $existingCount = Contact::where('value', $request->value)
                    ->where('type_contact_id', $typeContact->id)
                    ->where('module_id', $moduleRecord->id)
                    ->where('register_id', $registerId)
                    ->count();

                if ($existingCount > 0) {
                    return $this->validationError([
                        'value' => ['Este email já está cadastrado.']
                    ], 'Este email já está cadastrado.');
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
                'is_main' => $request->has('is_main'),
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
            return $this->notFound('Submódulo não implementado');
        }

        // Cria o registro
        $record = $modelClass::create($data);

        // Carrega relacionamentos
        if (!empty($config['with'])) {
            $record->load($config['with']);
        }

        return $this->created($record, ucfirst($submodule) . ' adicionado com sucesso!');
    }

    public function show(Request $request, string $module, string $code, string $submodule, string $s_code): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $id = decodeId($s_code);
        $item = $config['model']::with($config['with'])->findOrFail($id);

        return $this->success([
            $submodule => $item,
        ]);
    }

    public function update(Request $request, string $module, string $code, string $submodule, string $s_code): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $id = decodeId($s_code);
        $record = $config['model']::findOrFail($id);

        // Validação e atualização específica por submódulo
        if ($submodule === 'contacts') {
            // Validação básica primeiro
            $basicValidation = $request->validate([
                'type_contact_id' => 'required|exists:type_contacts,id',
            ]);

            // Busca o tipo de contato para verificar se é Email
            $typeContact = TypeContact::find($request->type_contact_id);

            // Define regras de validação para o valor
            $rules = [
                'value' => 'required|string|max:255',
            ];

            // Se for Email, adiciona validação de unicidade e formato (exceto o próprio registro)
            if ($typeContact && $typeContact->name === 'Email') {
                // Verifica se já existe um email igual para esta pessoa (exceto o próprio registro)
                $existingCount = Contact::where('value', $request->value)
                    ->where('type_contact_id', $typeContact->id)
                    ->where('module_id', $record->module_id)
                    ->where('register_id', $record->register_id)
                    ->where('id', '!=', $id)
                    ->count();

                if ($existingCount > 0) {
                    return $this->validationError([
                        'value' => ['Este email já está cadastrado.']
                    ], 'Este email já está cadastrado.');
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
                'content' => $validated['content'],
                'status' => $request->input('status', 1) == 1,
            ]);
        } else {
            return $this->notFound('Submódulo não implementado');
        }

        // Carrega relacionamentos
        if (!empty($config['with'])) {
            $record->load($config['with']);
        }

        return $this->success($record, ucfirst($submodule) . ' atualizado com sucesso!');
    }

    public function destroy(Request $request, string $module, string $code, string $submodule, string $s_code): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $id = decodeId($s_code);
        $item = $config['model']::findOrFail($id);

        // Para files: deletar arquivo físico também
        if ($submodule === 'files' && $item->path) {
            Storage::disk('public')->delete($item->path);
        }

        $item->delete();

        return $this->deleted();
    }

    public function restore(Request $request, string $module, string $code, string $submodule, string $s_code): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $id = decodeId($s_code);
        $item = $config['model']::withTrashed()->findOrFail($id);
        $item->restore();

        return $this->restored();
    }

    public function reorder(Request $request, string $module, string $code, string $submodule): JsonResponse
    {
        $config = $this->resolveSubmodule($submodule);
        if (!$config) {
            return $this->notFound("Submódulo '{$submodule}' não encontrado");
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer',
        ]);

        foreach ($request->order as $id => $order) {
            $config['model']::where('id', $id)->update(['order' => $order]);
        }

        return $this->success(null, 'Ordem atualizada com sucesso');
    }
}
