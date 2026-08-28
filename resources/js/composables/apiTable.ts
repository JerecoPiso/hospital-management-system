import { computed, onMounted, ref } from "vue";
import debounce from "lodash-es/debounce";

export interface ApiTableMeta {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
}

export const emptyMeta = (perPage = 15): ApiTableMeta => ({
    total: 0,
    per_page: perPage,
    current_page: 1,
    last_page: 1,
});

export interface ApiTableParams {
    search?: string;
    page: number;
    per_page: number;
}

interface UseApiTableOptions {
    perPage?: number;
    debounceMs?: number;
    immediate?: boolean;
}

/**
 * Drives a PrimeVue DataTable in lazy (server-side) mode: keeps `page`,
 * `per_page` and a debounced `search` term, calls `fetcher` whenever any of
 * them change, and exposes `total` from a reactive meta getter.
 *
 * Usage:
 *   const { search, rows, first, total, loading, onPage, onSearch, reload } =
 *     useApiTable((params) => store.read(params), () => store.meta);
 */
export function useApiTable(
    fetcher: (params: ApiTableParams) => Promise<unknown> | unknown,
    metaGetter: () => ApiTableMeta | undefined,
    options: UseApiTableOptions = {}
) {
    const perPage = options.perPage ?? 15;

    const search = ref("");
    const page = ref(1);
    const rows = ref(perPage);
    const first = ref(0);
    const loading = ref(false);

    const total = computed(() => metaGetter()?.total ?? 0);

    const load = async () => {
        loading.value = true;
        try {
            await fetcher({
                search: search.value.trim() || undefined,
                page: page.value,
                per_page: rows.value,
            });
        } finally {
            loading.value = false;
        }
    };

    const onPage = (event: { page: number; rows: number; first: number }) => {
        page.value = event.page + 1;
        rows.value = event.rows;
        first.value = event.first;
        load();
    };

    const runSearch = debounce(() => {
        page.value = 1;
        first.value = 0;
        load();
    }, options.debounceMs ?? 350);

    const onSearch = () => runSearch();

    const reload = () => load();

    if (options.immediate !== false) {
        onMounted(load);
    }

    return { search, page, rows, first, total, loading, load, reload, onPage, onSearch };
}
