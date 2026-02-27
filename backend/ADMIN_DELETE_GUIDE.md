# Admin Panel – Delete Guide

This guide explains how **delete** works in the backend admin panel and how to add or use it.

---

## 1. How delete works (overview)

- **Route:** Admin delete routes are **GET** (e.g. `GET /admin/categories/123/delete`). The admin prefix comes from `getAdminPanelUrlPrefix()` (often `admin`).
- **Controller:** A `destroy($id)` (or `delete($id)`) method loads the record, checks permission, deletes it, then redirects back or to the list with a success toast.
- **UI:** List/edit views include a delete **button** via `@include('admin.includes.delete_button', ['url' => ...])`. Clicking it shows a confirm dialog, then sends the user to that URL.
- **Permission:** The button is wrapped in `@can('admin_..._delete')`. Your admin **role** must have the matching permission to see and use delete.

---

## 2. If you don’t see the delete button

1. **Check permission**  
   The delete button is only shown if the logged-in user **can** do the delete permission, e.g.:
   - Categories: `admin_categories_delete`
   - Users: `admin_users_delete`
   - Webinars: `admin_webinars_delete`  
   So: go to **Admin → Roles**, edit the role you use, and enable the corresponding “delete” permission for that resource.

2. **Confirm the route exists**  
   In `routes/admin.php` there should be a route like:
   - `Route::get('/{id}/delete', 'CategoryController@destroy');`
   (under the right prefix group, e.g. `categories`.)

If the permission is enabled and the route exists, the delete button should appear in the list/edit view for that resource.

---

## 3. How to add delete for a new resource

Use the **Categories** flow as a template.

### Step 1: Route

In `routes/admin.php`, inside the correct group and prefix, add:

```php
Route::get('/{id}/delete', 'YourController@destroy');
```

Example for a “Items” resource under `/admin/items`:

```php
Route::group(['prefix' => 'items'], function () {
    Route::get('/', 'ItemController@index');
    Route::get('/create', 'ItemController@create');
    Route::post('/store', 'ItemController@store');
    Route::get('/{id}/edit', 'ItemController@edit');
    Route::post('/{id}/update', 'ItemController@update');
    Route::get('/{id}/delete', 'ItemController@destroy');  // delete
});
```

### Step 2: Controller method

In `app/Http/Controllers/Admin/YourController.php` add:

```php
public function destroy(Request $request, $id)
{
    $this->authorize('admin_items_delete');  // use your permission name

    $item = YourModel::findOrFail($id);
    $item->delete();

    $toastData = [
        'title' => trans('public.request_success'),
        'msg' => trans('public.record_deleted'),
        'status' => 'success'
    ];

    return redirect(getAdminPanelUrl() . '/items')->with(['toast' => $toastData]);
}
```

- Replace `admin_items_delete` with the permission you use for this resource.
- Replace `YourModel` and `/items` with your model and list URL.

### Step 3: Permission

- In the **Roles** (and permissions) setup, add a permission like `admin_items_delete` and assign it to the roles that may delete.

### Step 4: Delete button in the view

In the **list** view (e.g. `resources/views/admin/items/lists.blade.php`), in the actions column:

```blade
@can('admin_items_delete')
    @include('admin.includes.delete_button', [
        'url' => getAdminPanelUrl() . '/items/' . $item->id . '/delete',
        'deleteConfirmMsg' => trans('admin/main.delete_confirm_msg')  // or a custom message
    ])
@endcan
```

Optional parameters for `delete_button`:

- `url` – **required.** Full URL to the delete route.
- `deleteConfirmMsg` – optional; custom confirm text.
- `btnText` – optional; button label (e.g. `trans('public.delete')`).
- `btnClass` – optional; extra CSS classes.

After that, the admin panel will have a delete action that shows a confirm dialog and then deletes the record.

---

## 4. Existing delete routes (reference)

Many admin resources already have delete. Examples from `routes/admin.php`:

| Resource        | Route (under admin prefix)     | Controller method      |
|----------------|--------------------------------|-------------------------|
| Categories     | `GET /categories/{id}/delete`  | CategoryController@destroy |
| Users          | `GET /users/{id}/delete`       | UserController@destroy |
| Webinars       | `GET /webinars/{id}/delete`    | WebinarController@destroy |
| Blog           | `GET /blog/{id}/delete`        | BlogController@delete  |
| Supports       | `GET /supports/{id}/delete`    | SupportsController@delete |
| Notifications  | `GET /notifications/{id}/delete` | NotificationsController@delete |
| Reviews        | `GET /reviews/{id}/delete`     | ReviewsController@delete |
| Contacts       | `GET /contacts/{id}/delete`    | ContactController@delete |
| Pages          | `GET /pages/{id}/delete`       | PagesController@delete |
| Bundles        | `GET /bundles/{id}/delete`     | BundleController@destroy |
| …              | …                              | … |

The delete **button** appears on the list (and sometimes edit) view for each of these. If you don’t see it, enable the corresponding “delete” permission for your role (see section 2).
