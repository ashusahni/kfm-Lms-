import { useState } from "react";
import { Link } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useConfig } from "@/context/ConfigContext";
import { authService } from "@/services/auth";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";
import { motion } from "framer-motion";

const schema = z.object({
  username: z.string().min(1, "Required"),
});

type FormData = z.infer<typeof schema>;

const ForgotPassword = () => {
  const { appConfig } = useConfig();
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const isEmail =
    appConfig == null ? true : appConfig.register_method === "email";

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { username: "" },
  });

  const onSubmit = async (data: FormData) => {
    setError(null);
    setLoading(true);
    try {
      const payload: { email?: string; mobile?: string; country_code?: string } = {};
      if (isEmail) payload.email = data.username.trim();
      else {
        payload.mobile = data.username.replace(/\D/g, "");
        payload.country_code = "+91";
      }
      const res = await authService.forgotPassword(payload);
      if (res?.success) setSuccess(true);
      else setError(res?.message ?? "Request failed. Try again.");
    } catch (e) {
      setError(e instanceof Error ? e.message : "Request failed.");
    } finally {
      setLoading(false);
    }
  };

  if (success) {
    return (
      <AnimatedBackground variant="dots">
        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          className="w-full max-w-md px-4"
        >
          <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
            <CardHeader>
              <CardTitle className="text-2xl font-display">Check your {isEmail ? "email" : "phone"}</CardTitle>
              <CardDescription>
                If an account exists, we&apos;ve sent instructions to reset your password.
              </CardDescription>
            </CardHeader>
            <CardContent>
              <Link to="/login">
                <Button className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90">Back to Sign in</Button>
              </Link>
            </CardContent>
          </Card>
        </motion.div>
      </AnimatedBackground>
    );
  }

  return (
    <AnimatedBackground variant="grid">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="w-full max-w-md px-4"
      >
        <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
          <CardHeader className="space-y-1 text-center">
            <CardTitle className="text-2xl font-display">Forgot password</CardTitle>
            <CardDescription>
              Enter your {isEmail ? "email" : "mobile"} and we&apos;ll send reset instructions
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              {error && (
                <Alert variant="destructive">
                  <AlertDescription>{error}</AlertDescription>
                </Alert>
              )}
              <div className="space-y-2">
                <Label htmlFor="username">{isEmail ? "Email" : "Mobile"}</Label>
                <Input
                  id="username"
                  type={isEmail ? "email" : "text"}
                  placeholder={isEmail ? "you@example.com" : "Mobile number"}
                  autoComplete="username"
                  className="transition-shadow focus-visible:ring-2"
                  {...register("username")}
                />
                {errors.username && (
                  <p className="text-sm text-destructive">{errors.username.message}</p>
                )}
              </div>
              <Button type="submit" className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90" disabled={loading}>
                {loading ? "Sending…" : "Send reset link"}
              </Button>
            </form>
            <p className="mt-4 text-center text-sm text-muted-foreground">
              <Link to="/login" className="text-primary font-medium hover:underline">
                Back to Sign in
              </Link>
            </p>
          </CardContent>
        </Card>
      </motion.div>
    </AnimatedBackground>
  );
};

export default ForgotPassword;
