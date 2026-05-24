import { useState } from 'react';
import { useForm } from 'react-hook-form';
import {
  Paper,
  TextField,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Typography,
  Box,
  Button,
} from '@mui/material';

interface ProductRow {
  name: string;
  honeyBrew: number;
  arenBrew: number;
  pandanBrew: number;
  caramelBrew: number;
  vanillaBrew: number;
  butterscotchBrew: number;
  kopsuBrew: number;
  americanoVanilla: number;
  americanoApple: number;
  americano: number;
  matchaBrew: number;
  cokelatBrew: number;
  taroBrew: number;
}

interface FormData {
  tanggal: string;
  namaRider: string;
  namaAdminPemeriksa: string;
  ttdRider: string;
  produkKeluar: ProductRow;
  produkTambahan: ProductRow;
  produkRetur: ProductRow;
  produkLaku: ProductRow;
  totalUsang: ProductRow;
}

const createEmptyRow = (): ProductRow => ({
  name: '',
  honeyBrew: 0,
  arenBrew: 0,
  pandanBrew: 0,
  caramelBrew: 0,
  vanillaBrew: 0,
  butterscotchBrew: 0,
  kopsuBrew: 0,
  americanoVanilla: 0,
  americanoApple: 0,
  americano: 0,
  matchaBrew: 0,
  cokelatBrew: 0,
  taroBrew: 0,
});

export default function App() {
  const { register, handleSubmit, watch } = useForm<FormData>({
    defaultValues: {
      tanggal: '',
      namaRider: '',
      namaAdminPemeriksa: '',
      ttdRider: '',
      produkKeluar: createEmptyRow(),
      produkTambahan: createEmptyRow(),
      produkRetur: createEmptyRow(),
      produkLaku: createEmptyRow(),
      totalUsang: createEmptyRow(),
    },
  });

  const [cash, setCash] = useState(0);
  const [total, setTotal] = useState(0);
  const [qris, setQris] = useState(0);

  const onSubmit = (data: FormData) => {
    console.log('Form Data:', data);
  };

  const columns = [
    { key: 'honeyBrew', label: 'Honey Brew', price: 0 },
    { key: 'arenBrew', label: 'Aren Brew', price: 0 },
    { key: 'pandanBrew', label: 'Pandan Brew', price: 0 },
    { key: 'caramelBrew', label: 'Caramel Brew', price: 0 },
    { key: 'vanillaBrew', label: 'Vanilla Brew', price: 0 },
    { key: 'butterscotchBrew', label: 'Butterscotch Brew', price: 0 },
    { key: 'kopsuBrew', label: 'Kopsu Brew', price: 0 },
    { key: 'americanoVanilla', label: 'Americano Vanilla', price: 0 },
    { key: 'americanoApple', label: 'Americano Apple', price: 0 },
    { key: 'americano', label: 'Americano', price: 0 },
    { key: 'matchaBrew', label: 'Matcha Brew', price: 0 },
    { key: 'cokelatBrew', label: 'Cokelat Brew', price: 0 },
    { key: 'taroBrew', label: 'Taro Brew', price: 0 },
  ];

  const rows = [
    { key: 'produkKeluar', label: 'Produk Keluar' },
    { key: 'produkTambahan', label: 'Produk Tambahan' },
    { key: 'produkRetur', label: 'Produk Retur' },
    { key: 'produkLaku', label: 'Produk Laku' },
    { key: 'totalUsang', label: 'Total Usang' },
  ];

  return (
    <div className="min-h-screen bg-gray-100 p-8">
      <Paper elevation={3} sx={{ maxWidth: 1400, margin: '0 auto', p: 4 }}>
        <Typography variant="h4" align="center" gutterBottom sx={{ mb: 4, fontWeight: 600 }}>
          TETHER BREW OFFICE
        </Typography>

        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <TextField
              fullWidth
              label="TANGGAL"
              type="date"
              InputLabelProps={{ shrink: true }}
              {...register('tanggal')}
            />
            <TextField
              fullWidth
              label="NAMA RIDER"
              {...register('namaRider')}
            />
            
            
          </div>

          <Typography variant="subtitle2" sx={{ mb: 1, color: 'text.secondary' }}>
            Krt. - Jenis Varian Minuman
          </Typography>

          <Box sx={{ mb: 2 }}>
            <Typography variant="caption" sx={{ display: 'inline-block', mr: 3 }}>
              Harga 12 Ribu
            </Typography>
            <Typography variant="caption" sx={{ display: 'inline-block', mr: 3 }}>
              8 Ribu
            </Typography>
            <Typography variant="caption" sx={{ display: 'inline-block' }}>
              10 Ribu
            </Typography>
          </Box>

          <TableContainer sx={{ mb: 4, overflowX: 'auto' }}>
            <Table size="small" sx={{ minWidth: 1200 }}>
              <TableHead>
                <TableRow>
                  <TableCell sx={{ fontWeight: 600, bgcolor: 'grey.100' }}>Nama Produk</TableCell>
                  {columns.map((col) => (
                    <TableCell key={col.key} align="center" sx={{ fontWeight: 600, bgcolor: 'grey.100', minWidth: 60 }}>
                      {col.label}
                    </TableCell>
                  ))}
                </TableRow>
              </TableHead>
              <TableBody>
                {rows.map((row) => (
                  <TableRow key={row.key} hover>
                    <TableCell sx={{ fontWeight: 500 }}>{row.label}</TableCell>
                    {columns.map((col) => (
                      <TableCell key={col.key} align="center" sx={{ p: 1 }}>
                        <TextField
                          type="number"
                          size="small"
                          inputProps={{ min: 0, style: { textAlign: 'center', fontSize: '0.875rem' } }}
                          sx={{ width: 60 }}
                          {...register(`${row.key}.${col.key}` as any, { valueAsNumber: true })}
                        />
                      </TableCell>
                    ))}
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>

          <Box sx={{ display: 'flex', justifyContent: 'flex-end', gap: 2, mb: 3 }}>
            <Paper elevation={2} sx={{ p: 3, minWidth: 250 }}>
              <Typography variant="h6" gutterBottom>
                TOTAL
              </Typography>
              <TextField
                fullWidth
                label="Jumlah Setoran"
                type="number"
                size="small"
                sx={{ mb: 2 }}
              />
              <TextField
                fullWidth
                label="CASH"
                type="number"
                size="small"
                value={cash}
                onChange={(e) => setCash(Number(e.target.value))}
                sx={{ mb: 2 }}
              />
              <TextField
                fullWidth
                label="QRIS"
                type="number"
                size="small"
                value={qris}
                onChange={(e) => setQris(Number(e.target.value))}
                sx={{ mb: 2 }}
              />
              <TextField
                fullWidth
                label="TOTAL"
                type="number"
                size="small"
                value={total}
                onChange={(e) => setTotal(Number(e.target.value))}
              />
            </Paper>
          </Box>

          <Box sx={{ display: 'flex', gap: 2, justifyContent: 'flex-end' }}>
            <Button variant="outlined" size="large">
              Reset
            </Button>
            <Button variant="contained" type="submit" size="large">
              Submit
            </Button>
          </Box>
        </form>
      </Paper>
    </div>
  );
}